<?php

/**
 * @file
 * Contains \Drupal\poll\PollStorageControllerInterface.
 */

namespace Drupal\poll;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageControllerInterface;

/**
 * Defines a common interface for poll term entity controller classes.
 */
interface PollStorageControllerInterface extends EntityStorageControllerInterface {

  /**
   * Removed reference to terms from term_hierarchy.
   *
   * @param array
   *   Array of terms that need to be removed from hierarchy.
   */
  public function deletePoll($poll);

  /**
   * Updates terms hierarchy information with the hierarchy trail of it.
   *
   * @param \Drupal\Core\Entity\EntityInterface $term
   *   Poll entity that needs to be added to term hierarchy information.
   */
  public function updatePoll(EntityInterface $poll);

}
