<?php

/**
 * @file
 * Contains \Drupal\poll\Entity\PollInterface.
 */

namespace Drupal\poll;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityStorageControllerInterface;

/**
 * Provides an interface defining a poll entity.
 */
interface PollInterface extends ContentEntityInterface {
  public function getOptions();
  public function hasUserVoted();
}