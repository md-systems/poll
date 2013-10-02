<?php

/**
 * @file
 * Definition of Drupal\poll\PollStorageController.
 */

namespace Drupal\poll;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Entity\DatabaseStorageControllerNG;

/**
 * Defines a Controller class for poll terms.
 */
class PollStorageController extends DatabaseStorageControllerNG implements PollStorageControllerInterface {

  /**
   * Overrides Drupal\Core\Entity\DatabaseStorageController::create().
   *
   * @param array $values
   *   An array of values to set, keyed by property name.
   */
  public function create(array $values) {

    $entity = parent::create($values);
    return $entity;
  }


  public function deletePoll($poll) {
    // TODO:
  }

  /**
   * Updates terms hierarchy information with the hierarchy trail of it.
   *
   * @param \Drupal\Core\Entity\EntityInterface $term
   *   Poll entity that needs to be added to term hierarchy information.
   */
  public function updatePoll(EntityInterface $poll) {
    // TODO:
  }

}
