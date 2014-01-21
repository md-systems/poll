<?php

/**
 * @file
 * Contains \Drupal\poll\Controller\PollController.
 */

namespace Drupal\poll\Controller;

use Drupal\Component\Utility\String;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\node\NodeTypeInterface;
use Drupal\poll\PollInterface;

/**
 * Returns responses for Node routes.
 */
class PollController extends ControllerBase {

  /**
   * The _title_callback for the node.add route.
   *
   * @param \Drupal\node\NodeTypeInterface $node_type
   *   The current node.
   *
   * @return string
   *   The page title.
   */
  public function addPollTitle(PollInterface $poll) {
    return Xss::filter($poll->getLabel());
  }

}
