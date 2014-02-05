<?php

/**
 * @file
 * Definition of Drupal\poll\PollFormController.
 */

namespace Drupal\poll;

use Drupal\Component\Utility\String;
use Drupal\Core\Entity\ContentEntityFormController;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Language\Language;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for the poll poll edit forms.
 */
class PollFormController extends ContentEntityFormController {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, array &$form_state) {
    $poll = $this->entity;

    $form['question'] = array(
      '#type' => 'textfield',
      '#title' => t('Question'),
      '#default_value' => ($poll->isNew()) ? '' : $poll->getQuestion(),
      '#maxlength' => 255,
      '#required' => TRUE,
      '#weight' => -1,
    );

    // Poll attributes
    $duration = array(
      // 1-6 days.
      86400,
      2 * 86400,
      3 * 86400,
      4 * 86400,
      5 * 86400,
      6 * 86400,
      // 1-3 weeks (7 days).
      604800,
      2 * 604800,
      3 * 604800,
      // 1-3,6,9 months (30 days).
      2592000,
      2 * 2592000,
      3 * 2592000,
      6 * 2592000,
      9 * 2592000,
      // 1 year (365 days).
      31536000,
    );
    $duration = array(0 => t('Unlimited')) + drupal_map_assoc($duration, 'format_interval');

    $form['runtime'] = array(
      '#type' => 'select',
      '#title' => t('Poll duration'),
      '#default_value' => ($poll->isNew()) ? POLL_PUBLISHED : $poll->getRuntime(),
      '#options' => $duration,
      '#description' => t('After this period, the poll will be closed automatically.'),
      '#weight' => 0,
    );

    $form['anonymous_vote_allow'] = array(
      '#type' => 'radios',
      '#title' => $this->t('Anonymous votes allowed'),
      '#default_value' => ($poll->isNew()) ? 0 : $poll->isAnonymousVoteAllow(),
      '#options' => array($this->t('No'), $this->t('Yes')),
      '#weight' => 1,
    );
    $form['cancel_vote_allow'] = array(
      '#type' => 'radios',
      '#title' => $this->t('Cancel votes allowed'),
      '#default_value' => ($poll->isNew()) ? 0 : $poll->isCancelVoteAllow(),
      '#options' => array($this->t('No'), $this->t('Yes')),
      '#weight' => 2,
    );
    $form['result_vote_allow'] = array(
      '#type' => 'radios',
      '#title' => $this->t('View results allowed'),
      '#default_value' => ($poll->isNew()) ? 0 : $poll->isResultVoteAllow(),
      '#options' => array($this->t('No'), $this->t('Yes')),
      '#weight' => 3,
    );

    $form['status'] = array(
      '#type' => 'radios',
      '#title' => $this->t('Status'),
      '#default_value' => ($poll->isNew()) ? 1 : $poll->isActive(),
      '#options' => array($this->t('Closed'), $this->t('Active')),
      '#weight' => 4,
    );

    $form['langcode'] = array(
      '#title' => $this->t('Language'),
      '#type' => 'language_select',
      '#default_value' => $poll->language()->id,
      '#languages' => Language::STATE_ALL,
      '#weight' => 5,
    );

    return parent::form($form, $form_state, $poll);
  }

  /**
   * {@inheritdoc}
   */
  public function validate(array $form, array &$form_state) {
    $poll = $this->buildEntity($form, $form_state);
    // Check for duplicate titles.
//    $poll_storage_controller = $this->entityManager->getStorageController('poll_poll');
//    $result = $poll_storage_controller->getPollDuplicates($poll);
//    foreach ($result as $item) {
//      if (strcasecmp($item->title, $poll->label()) == 0) {
//        $this->setFormError('title', $form_state, $this->t('A poll named %poll already exists. Enter a unique title.', array('%poll' => $poll->label())));
//      }
//      if (strcasecmp($item->url, $poll->url->value) == 0) {
//        $this->setFormError('url', $form_state, $this->t('A poll with this URL %url already exists. Enter a unique URL.', array('%url' => $poll->url->value)));
//      }
//    }
    parent::validate($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function buildEntity(array $form, array &$form_state) {
    global $user;

    $poll = parent::buildEntity($form, $form_state);

    $poll->setQuestion($form_state['values']['question']);
    $poll->setAnonymousVoteAllow($form_state['values']['anonymous_vote_allow']);
    $poll->setCancelVoteAllow($form_state['values']['cancel_vote_allow']);
    $poll->setResultVoteAllow($form_state['values']['result_vote_allow']);
    $poll->setCreated(REQUEST_TIME);
    $poll->setRuntime($form_state['values']['runtime']);
    $poll->setAuthorId($user->id());
    ((bool) $form_state['values']['status']) ? $poll->activate() : $poll->close();

    return $poll;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, array &$form_state) {
    $poll = $this->entity;
    $insert = (bool) $poll->id();
    $poll->save();
    if ($insert) {
      drupal_set_message($this->t('The poll %poll has been updated.', array('%poll' => $poll->label())));
    }
    else {
      watchdog('poll', 'Poll %poll added.', array('%poll' => $poll->label()), WATCHDOG_NOTICE, l($this->t('view'), 'admin/config/services/poll'));
      drupal_set_message($this->t('The poll %poll has been added.', array('%poll' => $poll->label())));
    }

    $form_state['redirect_route']['route_name'] = 'poll.poll_list';

  }

  /**
   * {@inheritdoc}
   */
  public function delete(array $form, array &$form_state) {
    $form_state['redirect_route'] = array(
      'route_name' => 'poll.poll_delete',
      'route_parameters' => array('poll_poll' => $this->entity->id()),
    );
  }

}
