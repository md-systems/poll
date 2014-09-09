<?php

/**
 * @file
 * Contains \Drupal\poll\PollPostRenderCache.
 */

namespace Drupal\poll;

use Drupal\Core\Entity\EntityFormBuilderInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\field\Entity\FieldStorageConfig;

/**
 * Defines a service for poll post render cache callbacks.
 */
class PollPostRenderCache {

  /**
   * The entity manager service.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * The entity form builder service.
   *
   * @var \Drupal\Core\Entity\EntityFormBuilderInterface
   */
  protected $entityFormBuilder;

  /**
   * Constructs a new PollPostRenderCache object.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager service.
   * @param \Drupal\Core\Entity\EntityFormBuilderInterface $entity_form_builder
   *   The entity form builder service.
   */
  public function __construct(EntityManagerInterface $entity_manager, EntityFormBuilderInterface $entity_form_builder) {
    $this->entityManager = $entity_manager;
    $this->entityFormBuilder = $entity_form_builder;
  }

  /**
   * #post_render_cache callback; replaces placeholder with poll view form.
   *
   * @param array $element
   *   The renderable array that contains the to be replaced placeholder.
   * @param array $context
   *   An array with the following keys:
   *   - id: the poll ID
   *
   * @return array
   *   A renderable array containing the poll form.
   */
  public function renderViewForm(array $element, array $context) {
    $poll = $this->entityManager->getStorage('poll')->load($context['id']);

    $form = \Drupal::formBuilder()->getForm('Drupal\poll\Form\PollViewForm', $poll);
    // For all view modes except full and block (as block displays it as the
    // block title, display the question.
    $form['#view_mode'] = $context['view_mode'];
    if ($context['view_mode'] != 'full' && $context['view_mode'] != 'block') {
      if (isset($form['results'])) {
        $form['results']['#show_question'] = TRUE;
      }
      else {
        $form['#show_question'] = TRUE;
      }
    }
    // @todo This only works as long as assets are still tracked in a global
    //   static variable, see https://drupal.org/node/2238835
    $markup = drupal_render($form);

    $callback = 'poll.post_render_cache:renderViewForm';
    $placeholder = drupal_render_cache_generate_placeholder($callback, $context);
    $element['#markup'] = str_replace($placeholder, $markup, $element['#markup']);

    return $element;
  }

}
