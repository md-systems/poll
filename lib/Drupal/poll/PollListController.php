<?php

/**
 * Definition of Drupal\poll\PollListController.
 */

namespace Drupal\poll;

use Drupal\Core\Config\Entity\ConfigEntityListController;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageControllerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal;

/**
 * Provides a listing of contact categories.
 */
class PollListController extends ConfigEntityListController {

  /**
   * Constructs a new ConfigEntityListController object.
   *
   * We are making sure the poll entity has been properly configured with the
   * required poll_choice field type.
   *
   * @param string $entity_type
   *   The type of entity to be listed.
   * @param array $entity_info
   *   An array of entity info for the entity type.
   * @param \Drupal\Core\Entity\EntityStorageControllerInterface $storage
   *   The entity storage controller class.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke hooks on.
   */
  public function __construct($entity_type, array $entity_info, EntityStorageControllerInterface $storage, ModuleHandlerInterface $module_handler) {
    parent::__construct($entity_type, $entity_info, $storage, $module_handler);

    // TODO: Move to a better place
    poll_install_choice_field();

  }

  /**
   * Overrides Drupal\Core\Entity\EntityListController::buildHeader().
   */
  public function buildHeader() {
    $header['question'] = t('Question');
    $header['status'] = t('Status');
    $header['created'] = t('Created');
    $header['votes'] = t('Votes');
    $header['operations'] = t('Operations');
    return $header + parent::buildHeader();
  }

  /**
   * Overrides Drupal\Core\Entity\EntityListController::buildRow().
   */
  public function buildRow(EntityInterface $entity) {
    $row['question'] = $entity->getQuestion();
    $row['status'] = ($entity->isActive()) ? 'Y' : 'N';
    $row['created'] = Drupal::service('date')->format($entity->getCreated(), 'long');
    $row['votes'] = 5; // TODO: add up all votes submitted for this poll
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function getOperations(EntityInterface $entity) {
    $operations = parent::getOperations($entity);
    $uri = $entity->uri();

    if (isset($operations['edit'])) {
      $operations['edit']['href'] = str_replace('entity/', '', $uri['path']) . '/edit';
    }
    if (isset($operations['delete'])) {
      $operations['delete']['href'] = str_replace('entity/', '', $uri['path']) . '/delete';
    }

    return $operations;
  }

}
