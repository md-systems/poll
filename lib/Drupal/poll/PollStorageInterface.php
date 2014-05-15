<?php

/**
 * @file
 * Contains \Drupal\poll\PollStorageInterface.
 */

namespace Drupal\poll;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines a common interface for poll entity controller classes.
 */
interface PollStorageInterface extends EntityStorageInterface {

  /**
   * Provides a list of duplicate polls.
   *
   * @param \Drupal\poll\Entity\PollInterface $poll
   *   The poll entity.
   *
   * @return
   *   An array with the list of duplicated polls.
   */
  public function getPollDuplicates(PollInterface $poll);

  public function saveVote(array $options);

  public function cancelVote(PollInterface $poll, AccountInterface $account = NULL);

  /**
   * Get total votes for a poll.
   *
   * @param PollInterface $poll
   *
   * @return mixed
   */
  public function getTotalVotes(PollInterface $poll);

  /**
   * Get all votes for a poll.
   *
   * @param PollInterface $poll
   *
   * @return mixed
   */
  public function getVotes(PollInterface $poll);

  /**
   * Delete a user's votes for a poll.
   *
   * @param PollInterface $poll
   *
   * @return mixed
   */
  public function deleteVotes(PollInterface $poll);

  /**
   * Get a user's votes for a poll.
   *
   * @param PollInterface $poll
   *
   * @return mixed
   */
  public function getUserVote(PollInterface $poll);

  public function getMostRecentPoll();

}
