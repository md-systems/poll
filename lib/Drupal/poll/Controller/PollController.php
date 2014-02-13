<?php

/**
 * @file
 * Contains \Drupal\poll\Controller\PollController.
 */

namespace Drupal\poll\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\poll\PollInterface;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for poll module routes.
 */
class PollController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection;
   */
  protected $database;

  /**
   * Constructs a \Drupal\poll\Controller\PollController object.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   */
  public function __construct(Connection $database) {
    $this->database = $database;
  }

  /**
   * {inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }

  /**
   * Presents the poll poll creation form.
   *
   * @return array
   *   A form array as expected by drupal_render().
   */
  public function pollAdd() {
    $entity_manager = $this->entityManager();
    $poll = $entity_manager->getStorageController('poll')
      ->create(array(
        'refresh' => 3600,
      ));
    return $entity_manager->getForm($poll);
  }

  /**
   * Route title callback.
   *
   * @param \Drupal\poll\PollInterface $poll_poll
   *   The poll poll.
   *
   * @return string
   *   The poll label.
   */
  public function pollTitle(PollInterface $poll_poll) {
    return Xss::filter($poll_poll->label());
  }

}
