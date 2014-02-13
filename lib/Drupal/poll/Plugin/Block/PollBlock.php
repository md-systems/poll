<?php

/**
 * @file
 * Contains \Drupal\poll\Plugin\Block\PollBlock.
 */

namespace Drupal\poll\Plugin\Block;

use Drupal\Core\Session\AccountInterface;
use Drupal\block\BlockBase;

/**
 * Provides a 'Poll' block.
 *
 * @Block(
 *   id = "poll_block",
 *   admin_label = @Translation("Most recent poll"),
 *   category = @Translation("Forms")
 * )
 */
class PollBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function access(AccountInterface $account) {
    return $account->hasPermission('access polls');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    // @todo: load most recent poll
    $poll = entity_load('poll', 1);

    // @todo: display the form
    return array();
  }

}
