<?php

/**
 * @file
 * Contains \Drupal\aggregator\FeedViewBuilder.
 */

namespace Drupal\poll;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityViewBuilder;

/**
 * Render controller for polls.
 */
class PollViewBuilder extends EntityViewBuilder {

  /**
   * {@inheritdoc}
   */
  public function view(EntityInterface $entity, $view_mode = 'full', $langcode = NULL) {
    $output['#poll'] = $entity;
    $output['poll'] = array(
      '#lazy_builder' => [
        'poll.post_render_cache:renderViewForm',
        [
          'id' => $entity->id(),
          'view_mode' => $view_mode,
        ],
      ],
      '#create_placeholder' => TRUE,
      '#cache' => [
        'tags' => $entity->getCacheTags(),
      ],
    );

    return $output;

  }

}
