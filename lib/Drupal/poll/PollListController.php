<?php

/**
 * Definition of Drupal\poll\PollListController.
 */

namespace Drupal\poll;

use Drupal\Core\Config\Entity\ConfigEntityListController;
use Drupal\Core\Entity\EntityInterface;


/**
 * Provides a listing of contact categories.
 */
class PollListController extends ConfigEntityListController {

  /**
   * Overrides Drupal\Core\Entity\EntityListController::buildHeader().
   */
  public function buildHeader() {
    $header['question'] = t('Question');
    $header['status'] = t('Status');
    $header['created'] = t('Created');
    $header['votes'] = t('Votes'); // TODO: computed field?
    $header['operations'] = t('Operations');
    return $header + parent::buildHeader();
  }

  /**
   * Overrides Drupal\Core\Entity\EntityListController::buildRow().
   */
  public function buildRow(EntityInterface $entity) {
    $row['question'] = $this->getQuestion($entity);
    $row['status'] = $this->getStatus($entity);
    $row['created'] = $this->getCreated($entity);
    $row['votes'] = $this->getVotes($entity); // TODO:
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  // TODO:
  public function getOperations(EntityInterface $entity) {
    $operations = parent::getOperations($entity);

    return $operations;
  }

}
