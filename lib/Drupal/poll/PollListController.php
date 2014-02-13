<?php

/**
 * Definition of Drupal\poll\PollListController.
 */

namespace Drupal\poll;

use Drupal;
use Drupal\Core\Entity\EntityListController;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityStorageControllerInterface;


/**
 * Provides a listing of polls.
 */
class PollListController extends EntityListController {

  /**
   *  Constructs a new EntityListController object.
   *
   * @param EntityTypeInterface $entity_info
   *    An array of entity info for the entity type.
   * @param EntityStorageControllerInterface $storage
   *    The entity storage controller class.
   */
  public function __construct(EntityTypeInterface $entity_info, EntityStorageControllerInterface $storage) {
    parent::__construct($entity_info, $storage);
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
    $poll_storage_controller = \Drupal::entityManager()
      ->getStorageController($entity->entityType());

    $row['question'] = l($entity->label(), 'poll/' . $entity->id());
    $row['status'] = ($entity->isActive()) ? t('Y') : t('N');
    $row['created'] = ($entity->getCreated()) ? Drupal::service('date')
      ->format($entity->getCreated(), 'long') : t('n/a');
    $row['votes'] = $poll_storage_controller->getTotalVotes($entity);
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function getOperations(EntityInterface $entity) {
    $uri = $entity->normaliseUri();
    $operations = parent::getOperations($entity);

    if (isset($operations['edit'])) {
      $operations['edit']['href'] = $uri['path'] . '/edit';
    }
    if (isset($operations['delete'])) {
      $operations['delete']['href'] = $uri['path'] . '/delete';
    }

    return $operations;
  }

}
