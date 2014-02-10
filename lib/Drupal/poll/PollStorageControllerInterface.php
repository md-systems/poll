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

  public function getTotalVotes(PollInterface $poll);
  public function getVotes(PollInterface $poll);
  public function deleteVotes(PollInterface $poll);
  public function getUserVote(PollInterface $poll);
  public function saveVote(array $fields);

}
