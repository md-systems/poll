<?php

/**
 * @file
 * Contains \Drupal\poll\PollFormController.
 */

namespace Drupal\poll;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the poll edit forms.
 */
class PollFormController extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $poll = $this->entity;
    // @todo: convert to a language selection widget defined in the base field.
    //   Blocked on https://drupal.org/node/2226493 which adds a generic
    //   language widget.
    $form['langcode'] = array(
      '#title' => $this->t('Language'),
      '#type' => 'language_select',
      '#default_value' => $poll->language()->getId(),
      '#languages' => LanguageInterface::STATE_ALL,
      '#weight' => -4,
    );

    return parent::form($form, $form_state, $poll);
  }

  public function buildEntity(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\poll\PollInterface $entity */
    $entity = parent::buildEntity($form, $form_state);

    if ($entity->isNew()) {
      $entity->setCreated(REQUEST_TIME);
    }
    return $entity;
  }

  /**
   * {@inheritdoc}
   */
  public function validate(array $form, FormStateInterface $form_state) {
    $poll = $this->buildEntity($form, $form_state);
    // Check for duplicate titles.
    $poll_storage = $this->entityManager->getStorage('poll');
    $result = $poll_storage->getPollDuplicates($poll);
    foreach ($result as $item) {
      if (strcasecmp($item->label(), $poll->label()) == 0) {
        $form_state->setErrorByName('question', $this->t('A feed named %feed already exists. Enter a unique question.', array('%feed' => $poll->label())));
      }
    }
    parent::validate($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $poll = $this->entity;
    $insert = (bool) $poll->id();
    $poll->save();
    if ($insert) {
      drupal_set_message($this->t('The poll %poll has been updated.', array('%poll' => $poll->label())));
    }
    else {
      watchdog('poll', 'Poll %poll added.', array('%poll' => $poll->label()), WATCHDOG_NOTICE, l($this->t('View'), 'admin/config/services/aggregator'));
      drupal_set_message($this->t('The poll %poll has been added.', array('%poll' => $poll->label())));
    }

    $form_state->setRedirect('poll.poll_list');
  }

}
