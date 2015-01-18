<?php

/**
 * Contains \Drupal\poll\PollListController.
 */

namespace Drupal\poll;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Defines a class to build a listing of user role entities.
 *
 * @see \Drupal\user\Entity\Role
 */
class PollListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'poll_list_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {

    $header['question'] = t('Question');
    $header['author'] = t('Author');
    $header['votes'] = t('Votes');
    $header['status'] = t('Status');
    $header['created'] = t('Created');
    $header['operations'] = t('Operations');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $pollStorage = \Drupal::entityManager()->getStorage('poll');

    $row['question'] = $entity->link($entity->label());
    $row['author']['data'] = array(
      '#theme' => 'username',
      '#account' => $entity->getOwner(),
    );
    $row['votes'] = $pollStorage->getTotalVotes($entity);
    $row['status'] = ($entity->isOpen()) ? t('Y') : t('N');
    $row['created'] = ($entity->getCreated()) ? \Drupal::service('date.formatter')
      ->format($entity->getCreated(), 'long') : t('n/a');
    return $row + parent::buildRow($entity);
  }

}
