<?php

/**
 * @file
 * Definition of Drupal\poll\PollStorageController.
 */

namespace Drupal\poll;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Entity\DatabaseStorageController;

/**
 * Defines a Controller class for poll terms.
 */
class PollStorageController extends DatabaseStorageController implements PollStorageControllerInterface {


  public function deletePoll($poll) {

  }

  /**
   * Updates terms hierarchy information with the hierarchy trail of it.
   *
   * @param \Drupal\Core\Entity\EntityInterface $term
   *   Poll entity that needs to be added to term hierarchy information.
   */
  public function updatePoll(EntityInterface $poll) {

  }

  public function onFieldCreate(FieldInterface $field) {

  }

}
