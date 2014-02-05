<?php

/**
 * @file
 * Definition of Drupal\poll\PollStorageController.
 */

namespace Drupal\poll;

use Drupal\poll\PollInterface;
use Drupal\Core\Entity\FieldableDatabaseStorageController;

/**
 * Controller class for poll's feeds.
 *
 * This extends the Drupal\Core\Entity\DatabaseStorageController class, adding
 * required special handling for feed entities.
 */
class PollStorageController extends FieldableDatabaseStorageController implements PollStorageControllerInterface {

  public function getTotalVotes(PollInterface $poll) {
    $query = $this->database->query("SELECT COUNT(chid) FROM {poll_vote} WHERE pid = :pid", array(':pid' => $poll->id()));
    return $query->fetchField();
  }

  public function deleteVotes(PollInterface $poll) {
    $this->database->delete('poll_vote')->condition('pid', $poll->id())->execute();
  }

}
