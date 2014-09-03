<?php

/**
 * @file
 * Contains \Drupal\aggregator\FeedViewBuilder.
 */

namespace Drupal\poll;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityViewBuilder;
use Drupal\poll\PollStorageInterface;

/**
 * Render controller for polls.
 */
class PollViewBuilder extends EntityViewBuilder {

  /**
   * {@inheritdoc}
   */
  public function view(EntityInterface $entity, $view_mode = 'full', $langcode = NULL) {
    $form = \Drupal::formBuilder()->getForm('Drupal\poll\Form\PollViewForm', $entity);
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
    return $form;
  }

}
