<?php

/**
 * @file
 * Contains \Drupal\poll\Plugin\Block\PollBlock.
 */

namespace Drupal\poll\Plugin\Block;

use Drupal\Core\Session\AccountInterface;
use Drupal\block\BlockBase;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\poll\Controller\PollController;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityStorageControllerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Poll' block.
 *
 * @Block(
 *   id = "poll_block",
 *   admin_label = @Translation("Most recent poll"),
 *   category = @Translation("Forms")
 * )
 */
//implements ContainerFactoryPluginInterface
class PollBlock extends BlockBase {

//  /**
//   * The entity storage controller for feeds.
//   *
//   * @var \Drupal\Core\Entity\EntityStorageControllerInterface
//   */
//  protected $storageController;
//
//  /**
//   * The database connection.
//   *
//   * @var \Drupal\Core\Database\Connection
//   */
//  protected $connection;
//
//  /**
//   * Constructs an AggregatorFeedBlock object.
//   *
//   * @param array $configuration
//   *   A configuration array containing information about the plugin instance.
//   * @param string $plugin_id
//   *   The plugin_id for the plugin instance.
//   * @param array $plugin_definition
//   *   The plugin implementation definition.
//   * @param \Drupal\Core\Entity\EntityStorageControllerInterface $storage_controller
//   *   The entity storage controller for feeds.
//   * @param \Drupal\Core\Database\Connection $connection
//   *   The database connection.
//   */
//  public function __construct(array $configuration, $plugin_id, array $plugin_definition, EntityStorageControllerInterface $storage_controller, Connection $connection) {
//    parent::__construct($configuration, $plugin_id, $plugin_definition);
//    $this->storageController = $storage_controller;
//    $this->connection = $connection;
//  }
//
//  /**
//   * {@inheritdoc}
//   */
//  public static function create(ContainerInterface $container, array $configuration, $plugin_id, array $plugin_definition) {
//    return new static(
//      $configuration,
//      $plugin_id,
//      $plugin_definition,
//      $container->get('entity.manager')->getStorageController('poll'),
//      $container->get('database')
//    );
//  }
//
//  /**
//   * {@inheritdoc}
//   */
//  public function access(AccountInterface $account) {
//    return $account->hasPermission('access polls');
//  }

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
