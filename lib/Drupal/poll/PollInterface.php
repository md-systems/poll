<?php

/**
 * @file
 * Contains \Drupal\poll\Entity\PollInterface.
 */

namespace Drupal\poll;

use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Provides an interface defining a poll entity.
 */
interface PollInterface extends ContentEntityInterface {
  /**
   * Get all vote options for a poll.
   *
   * @return mixed
   */
  public function getOptions();

  /**
   * Get the value for all vote options for a poll.
   *
   * @return mixed
   */
  public function getOptionValues();

  /**
   * @todo: this probably doesn't belong here.
   *
   * Check whether a user has voted in a poll.
   *
   * @return mixed
   */
  public function hasUserVoted();

}
