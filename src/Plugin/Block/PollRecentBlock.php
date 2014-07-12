<?php

/**
 * @file
 * Contains \Drupal\poll\Plugin\block\block\PollRecentBlock.
 */

namespace Drupal\poll\Plugin\Block;

use Drupal\block\BlockBase;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a 'Most recent poll' block.
 *
 * @Block(
 *   id = "poll_recent_block",
 *   admin_label = @Translation("Most recent poll"),
 *   category = @Translation("Lists (Views)")
 * )
 */
class PollRecentBlock extends BlockBase {

  /**
   * Stores the node ID of the latest poll.
   *
   * @var int
   */
  protected $record;

  /**
   * Overrides \Drupal\block\BlockBase::settings().
   */
  public function settings() {
    return array(
      'properties' => array(
        'administrative' => TRUE,
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function access(AccountInterface $account) {
    return $account->hasPermission('access polls');
  }

  /**
   * Implements \Drupal\block\BlockBase::build().
   */
  public function build() {
    $polls = \Drupal::entityManager()->getStorage('poll')->getMostRecentPoll();
    if ($polls) {
      $poll = reset($polls);
      // If we're viewing this poll, don't show this block.
//      $page = \Drupal::request()->attributes->get('poll');
//      if ($page instanceof PollInterface && $page->id() == $poll->id()) {
//        return;
//      }
      // @todo: new view mode using ajax
      return entity_view($poll, 'block');
    }
  }
}
