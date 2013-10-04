<?php

/**
 * @file
 * Definition of Drupal\poll\PollFormController.
 */

namespace Drupal\poll;

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

    $form['question'] = array(
      '#type' => 'textfield',
      '#title' => t('Question'),
      '#maxlength' => 255,
      '#required' => TRUE,
      '#weight' => -1,
    );

    $form['status'] = array(
      '#type' => 'radios',
      '#title' => $this->t('Status'),
      '#default_value' => ($poll->isNew()) ? 1 : $poll->get('status')
        ->getValue(),
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
      '#default_value' => $poll->get('runtime')->getValue(),
      '#options' => $duration,
      '#description' => t('After this period, the poll will be closed automatically.'),
      '#weight' => 2,
    );

    $form['anonymous_vote_allow'] = array(
      '#type' => 'radios',
      '#title' => $this->t('Anonymous votes allowed'),
      '#default_value' => ($poll->isNew()) ? 0 : $poll
        ->get('anonymous_vote_allowed')->getValue(),
      '#options' => array($this->t('No'), $this->t('Yes')),
      '#weight' => 3,
    );
    $form['cancel_vote_allow'] = array(
      '#type' => 'radios',
      '#title' => $this->t('Cancel votes allowed'),
      '#default_value' => ($poll->isNew()) ? 0 : $poll
        ->get('cancel_vote_allowed')->getValue(),
      '#options' => array($this->t('No'), $this->t('Yes')),
      '#weight' => 4,
    );
    $form['result_vote_allow'] = array(
      '#type' => 'radios',
      '#title' => $this->t('View results allowed'),
      '#default_value' => ($poll->isNew()) ? 0 : $poll
        ->get('result_vote_allowed')->getValue(),
      '#options' => array($this->t('No'), $this->t('Yes')),
      '#weight' => 5,
    );

    return parent::form($form, $form_state, $poll);

  }

  /**
   * {@inheritdoc}
   */
  public function buildEntity(array $form, array &$form_state) {
    //$poll = $this->entity;
    $poll = parent::buildEntity($form, $form_state);

    $poll->question->value = $form_state['values']['question'];
    $poll->anonymous_vote_allow->value = $form_state['values']['anonymous_vote_allow'];;
    $poll->cancel_vote_allow->value = $form_state['values']['cancel_vote_allow'];
    $poll->result_vote_allow->value = $form_state['values']['result_vote_allow'];
    $poll->created->value = REQUEST_TIME;
    $poll->langcode->value = 'und';
    $poll->runtime->value = $form_state['values']['runtime'];

//    $poll->field_choice[0]->choice = "first choice";
//    $poll->field_choice[0]->vote = 3;

    return $poll;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, array &$form_state) {
    $poll = $this->entity;

    $poll->save();
    $form_state['redirect'] = 'admin/structure/poll/add';

  }


//  public function submit(array $form, array &$form_state) {
//    // Build the block object from the submitted values.
//    $poll = parent::submit($form, $form_state);
//    $poll->question->value = 'Lorem lispum dolor amet vici xxx!!!';
//    $poll->anonymous_vote_allowed->value = 1;
//    $poll->cancel_vote_allowed->value = 0;
//    $poll->result_vote_allowed->value = 1;
//    $poll->created->value = strtotime('now');
//    $poll->langcode->value = 'und';
//    $poll->runtime->value = 4 * 86400;
//
////    $poll->field_choice[0]->choice = "first choice";
////    $poll->field_choice[0]->vote = 3;
//
//    return $poll;
//  }


}
