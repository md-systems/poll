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
  public function view(EntityInterface $entity, $view_mode = 'default', $langcode = NULL) {
    $build = parent::view($entity, $view_mode, $langcode);
//    if ($view_mode == 'block') {
//      var_dump($build);
//    }
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  protected function getBuildDefaults(EntityInterface $entity, $view_mode, $langcode) {
    // @todo: default & ajax view modes.
    $form = \Drupal::formBuilder()
      ->getForm('Drupal\poll\Form\PollViewForm', $entity);
    return $form;
  }

}
