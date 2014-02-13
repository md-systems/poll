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
 *   admin_label = @Translation("Poll"),
 *   category = @Translation("Forms")
 * )
 */
class PollBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function access(AccountInterface $account) {
  
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    return array();
  }

}
