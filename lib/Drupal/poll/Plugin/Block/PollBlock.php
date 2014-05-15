<?php

/**
 * @file
 * Contains \Drupal\poll\Plugin\Block\PollBlock.
 */

namespace Drupal\poll\Plugin\Block;

use Drupal\poll\PollStorageInterface;
use Drupal\block\BlockBase;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Cache\Cache;

/**
 * Provides a 'Poll' block with the latest poll.
 *
 * @Block(
 *   id = "poll_block",
 *   admin_label = @Translation("Most recent poll"),
 *   category = @Translation("Lists (Views)")
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
    $polls = \Drupal::entityManager()->getStorage('poll')->getMostRecentPoll();
    if ($polls) {
      $entity = reset($polls);
      $view = entity_view($entity, 'default');

      return $view;
    }

  }

}
