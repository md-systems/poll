<?php

/**
 * @file
 * Contains \Drupal\poll\PollStorageControllerInterface.
 */

namespace Drupal\poll;

use Drupal\Core\Session\AccountInterface;
use Drupal\poll\PollInterface;
use Drupal\Core\Entity\EntityStorageControllerInterface;

/**
 * Defines a common interface for poll entity controller classes.
 */
interface PollStorageControllerInterface extends EntityStorageControllerInterface {

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

  /**
   * Save votes cast by a user.
   *
   * @param array $fields
   *
   * @return mixed
   */
  public function saveVote(array $fields);

  /**
   * Cancel a user's vote(s) for a poll.
   *
   * @param PollInterface $poll
   * @param AccountInterface $account
   *
   * @return mixed
   */
  public function cancelVote(PollInterface $poll, AccountInterface $account);

}
