<?php

/**
 * @file
 * Definition of Drupal\poll\PollStorageController.
 */

namespace Drupal\poll;

use Drupal;
use Drupal\poll\PollInterface;
use Drupal\Core\Session\AccountInterface;
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
    $this->database->delete('poll_vote')->condition('pid', $poll->id())
      ->execute();
  }


  public function getUserVote(PollInterface $poll) {
    $uid = Drupal::currentUser()->id();
    if ($uid || $poll->anonymous_vote_allow->value) {
      if ($uid) {
        $query = $this->database->query("SELECT * FROM {poll_vote} WHERE pid = :pid AND uid = :uid", array(
          ':pid' => $poll->id(),
          ':uid' => $uid
        ));
      }
      else {
        $query = $this->database->query("SELECT * FROM {poll_vote} WHERE pid = :pid AND hostname = :hostname", array(
          ':pid' => $poll->id(),
          ':hostname' => Drupal::request()->getClientIp()
        ));
      }
      return $query->fetchObject();
    }
    return FALSE;
  }

  public function saveVote(array $options) {
    if (!is_array($options)) {
      return FALSE;
    }
    return $this->database->insert('poll_vote')->fields($options)->execute();
  }

  public function getVotes(PollInterface $poll) {
    $votes = array();
    // set votes for all options to 0
    $options = $poll->getOptions();
    foreach ($options as $id => $label) {
      $votes[$id] = 0;
    }

    $query = $this->database->query("SELECT chid, COUNT(chid) AS votes FROM {poll_vote} WHERE pid = :pid GROUP BY chid", array(':pid' => $poll->id()));
    $results = $query->fetchAll();
    // replace the votes for available options in the db.
    foreach ($results as $result) {
      $votes[$result->chid] = $result->votes;
    }

    return $votes;
  }

  public function cancelVote(PollInterface $poll, AccountInterface $account) {
    $this->database->delete('poll_vote')
      ->condition('pid', $poll->id())
      ->condition('uid', (!$account instanceof AccountInterface) ? Drupal::currentUser()
        ->id() : $account->id())
      ->execute();
  }

}
