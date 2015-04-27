<?php

/**
 * @file
 * Contains \Drupal\poll\views\field\PollTotalVotes.
 */

namespace Drupal\poll\Plugin\views\field;

use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;


/**
 * Field handler which shows the total votes for a poll.
 *
 * @ViewsField("poll_totalvotes")
 */
class PollTotalVotes extends FieldPluginBase {

  /**
   * @param \Drupal\views\ResultRow $values
   * @return mixed
   */
  function render(ResultRow $values) {
    $pollStorage = \Drupal::entityManager()->getStorage('poll');
    $entity = $values->_entity;
    return  $pollStorage->getTotalVotes($entity);
  }
}
