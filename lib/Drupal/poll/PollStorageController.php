<?php

/**
 * @file
 * Definition of Drupal\poll\PollStorageController.
 */

namespace Drupal\poll;

use Drupal;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Entity\FieldableDatabaseStorageController;

/**
 * Controller class for polls.
 *
 * This extends the Drupal\Core\Entity\DatabaseStorageController class, adding
 * required special handling for poll entities. All database queries are run
 * through this class.
 */
class PollStorageController extends FieldableDatabaseStorageController implements PollStorageControllerInterface {

  /**
   * Calcualte total votes for a poll.
   *
   * @param PollInterface $poll
   *
   * @return mixed
   */
  public function getTotalVotes(PollInterface $poll) {
    $query = $this->database->query("SELECT COUNT(chid) FROM {poll_vote} WHERE pid = :pid", array(':pid' => $poll->id()));
    return $query->fetchField();
  }

  /**
   * Delete all votes for a poll.
   *
   * @param PollInterface $poll
   */
  public function deleteVotes(PollInterface $poll) {
    $this->database->delete('poll_vote')->condition('pid', $poll->id())
      ->execute();
  }

  /**
   * Get a user's vote in a poll.
   *
   * @param PollInterface $poll
   *
   * @return bool
   */
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

  /**
   * Save a user's vote for a poll.
   *
   * @param array $options
   *
   * @return bool|Drupal\Core\Database\StatementInterface|int|null
   */
  public function saveVote(array $options) {
    if (!is_array($options)) {
      return FALSE;
    }
    return $this->database->insert('poll_vote')->fields($options)->execute();
  }

  /**
   * Get a count of votes for each option in the poll.
   *
   * @todo: At the moment we are assuming that value of each vote = 1. However,
   * this count should take into account the value for each option as set for
   * each option choice in the field_choice_vote column of poll__field_choice table.
   *
   * @param PollInterface $poll
   * @return array
   */
  public function getVotes(PollInterface $poll) {
    $votes = array();
    // set votes for all options to 0
    $options = $poll->getOptions();
    foreach ($options as $id => $label) {
      $votes[$id] = 0;
    }

    $query = $this->database->query("SELECT chid, COUNT(chid) AS votes FROM {poll_vote} WHERE pid = :pid GROUP BY chid", array(':pid' => $poll->id()));
    $results = $query->fetchAll();
    // Replace the count for options that have recorded votes in the database.
    foreach ($results as $result) {
      $votes[$result->chid] = $result->votes;
    }

    return $votes;
  }

  /**
   * Cancel a user's vote.
   *
   * @param PollInterface $poll
   * @param AccountInterface $account
   *   Default is the currently logged in user when the call is made.
   */
  public function cancelVote(PollInterface $poll, AccountInterface $account) {
    $this->database->delete('poll_vote')
      ->condition('pid', $poll->id())
      ->condition('uid', (!$account instanceof AccountInterface) ? Drupal::currentUser()
        ->id() : $account->id())
      ->execute();
  }

}
