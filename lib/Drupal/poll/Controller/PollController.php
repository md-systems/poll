<?php

/**
 * @file
 * Contains \Drupal\poll\Controller\PollController.
 */

namespace Drupal\poll\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\poll\PollInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides route responses for poll.module.
 */
class PollController extends ControllerBase {

  /**
   * Returns a rendered edit form to create a new poll.
   *
   * @param \Drupal\poll\PollInterface $poll
   *   The poll entity to be created.
   *
   * @return array
   *   The poll add form.
   */
  public function addForm() {
    $poll = $this->entityManager()->getStorageController('poll')->create(array('id' => $poll->id()));

    return $this->entityManager()->getForm($poll);
  }

}
