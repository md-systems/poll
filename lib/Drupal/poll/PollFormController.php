<?php

/**
 * @file
 * Definition of Drupal\poll\PollFormController.
 */

namespace Drupal\poll;

use Drupal\Component\Uuid\Uuid;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Entity\EntityFormControllerNG;
use Drupal\Core\Language\Language;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Base for controller for poll term edit forms.
 */
class PollFormController extends EntityFormControllerNG {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager')->getStorageController('poll'),
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, array &$form_state) {
    $poll = $this->entity;

//    $form = parent::form($form, $form_state, $poll);
//
//    $data = $form['field_choice']['und'][0]['#entity']->field_choice;
//    $string = check_plain(print_r($data, TRUE));
//    $string = '<pre>' . $string . '</pre>';
//    trigger_error(trim($string));

    $form['question'] = array(
      '#type' => 'textfield',
      '#title' => t('Question'),
      '#default_value' => ($poll->isNew()) ? '' : $poll->getQuestion(),
      '#maxlength' => 255,
      '#required' => TRUE,
      '#weight' => -1,
    );

    $form['status'] = array(
      '#type' => 'radios',
      '#title' => $this->t('Status'),
      '#default_value' => $poll->isActive(),
      '#options' => array($this->t('Closed'), $this->t('Active')),
      '#weight' => 1,
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
      '#default_value' => ($poll->isNew()) ? 0 : $poll->getRuntime(),
      '#options' => $duration,
      '#description' => t('After this period, the poll will be closed automatically.'),
      '#weight' => 2,
    );

    $form['anonymous_vote_allow'] = array(
      '#type' => 'radios',
      '#title' => $this->t('Anonymous votes allowed'),
      '#default_value' => $poll->isAnonymousVoteAllow(),
      '#options' => array($this->t('No'), $this->t('Yes')),
      '#weight' => 3,
    );
    $form['cancel_vote_allow'] = array(
      '#type' => 'radios',
      '#title' => $this->t('Cancel votes allowed'),
      '#default_value' => $poll->isCancelVoteAllow(),
      '#options' => array($this->t('No'), $this->t('Yes')),
      '#weight' => 4,
    );
    $form['result_vote_allow'] = array(
      '#type' => 'radios',
      '#title' => $this->t('View results allowed'),
      '#default_value' => $poll->isResultVoteAllow(),
      '#options' => array($this->t('No'), $this->t('Yes')),
      '#weight' => 5,
    );

    return parent::form($form, $form_state, $poll);

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
    $poll = parent::buildEntity($form, $form_state);
    $poll->save();
    $form_state['redirect'] = 'admin/structure/poll';
  }

}
