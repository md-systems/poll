<?php

/**
 * @file
 * Contains \Drupal\poll\PollPostRenderCache.
 */

namespace Drupal\poll;

use Drupal\Core\Entity\EntityManagerInterface;

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
   * Constructs a new PollPostRenderCache object.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager service.
   */
  public function __construct(EntityManagerInterface $entity_manager) {
    $this->entityManager = $entity_manager;
  }

  /**
   * Callback for #post_render_cache; replaces placeholder with poll view form.
   *
   * @param int $id
   *   The poll ID.
   * @param string $view_mode
   *   The view mode the poll should be rendered with.
   *
   * @return array
   *   A renderable array containing the poll form.
   */
  public function renderViewForm($id, $view_mode) {
    $poll = $this->entityManager->getStorage('poll')->load($id);

    if ($poll) {
      /** @var \Drupal\poll\Form\PollViewForm $form_object */
      $form_object = \Drupal::service('class_resolver')->getInstanceFromDefinition('Drupal\poll\Form\PollViewForm');
      $form_object->setPoll($poll);
      $form = \Drupal::formBuilder()->getForm($form_object, \Drupal::request());
      // For all view modes except full and block (as block displays it as the
      // block title, display the question.
      $form['#view_mode'] = $view_mode;
      if ($view_mode != 'full' && $view_mode != 'block') {
        if (isset($form['results'])) {
          $form['results']['#show_question'] = TRUE;
        }
        else {
          $form['#show_question'] = TRUE;
        }
      }
      $markup = $form;

    }
    else {
      $markup = ['#markup' => ''];
    }
    return $markup;
  }

}
