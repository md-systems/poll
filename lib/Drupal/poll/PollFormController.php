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
      '#default_value' => ($poll->isNew()) ? 1 : $poll->get('status')->getValue(),
      '#options' => array($this->t('Closed'), $this->t('Active')),
      '#weight' => 1,
    );

    // Poll attributes
    $duration = array(
      // 1-6 days.
      86400, 2 * 86400, 3 * 86400, 4 * 86400, 5 * 86400, 6 * 86400,
      // 1-3 weeks (7 days).
      604800, 2 * 604800, 3 * 604800,
      // 1-3,6,9 months (30 days).
      2592000, 2 * 2592000, 3 * 2592000, 6 * 2592000, 9 * 2592000,
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
      '#default_value' => ($poll->isNew()) ? 0 : $poll->get('anonymous_vote_allowed')->getValue(),
      '#options' => array($this->t('No'), $this->t('Yes')),
      '#weight' => 3,
    );
    $form['cancel_vote_allow'] = array(
      '#type' => 'radios',
      '#title' => $this->t('Cancel votes allowed'),
      '#default_value' => ($poll->isNew()) ? 0 : $poll->get('cancel_vote_allowed')->getValue(),
      '#options' => array($this->t('No'), $this->t('Yes')),
      '#weight' => 4,
    );
    $form['result_vote_allow'] = array(
      '#type' => 'radios',
      '#title' => $this->t('View results allowed'),
      '#default_value' => ($poll->isNew()) ? 0 : $poll->get('result_vote_allowed')->getValue(),
      '#options' => array($this->t('No'), $this->t('Yes')),
      '#weight' => 5,
    );

    return parent::form($form, $form_state, $poll);

  }

  /**
   * {@inheritdoc}
   */
  public function validate(array $form, array &$form_state) {
    parent::validate($form, $form_state);


  }

//  /**
//   * {@inheritdoc}
//   */
//  public function buildEntity(array $form, array &$form_state) {
//    $term = parent::buildEntity($form, $form_state);
//
//    // Prevent leading and trailing spaces in term names.
//    $term->name->value = trim($term->name->value);
//
//    // Convert text_format field into values expected by
//    // \Drupal\Core\Entity\Entity::save() method.
//    $description = $form_state['values']['description'];
//    $term->description->value = $description['value'];
//    $term->format->value = $description['format'];
//
//    // Assign parents with proper delta values starting from 0.
//    $term->parent = array_keys($form_state['values']['parent']);
//
//    return $term;
//  }
//
//  /**
//   * {@inheritdoc}
//   */
//  public function save(array $form, array &$form_state) {
//    $term = $this->entity;
//
//    switch ($term->save()) {
//      case SAVED_NEW:
//        drupal_set_message($this->t('Created new term %term.', array('%term' => $term->label())));
//        watchdog('poll', 'Created new term %term.', array('%term' => $term->label()), WATCHDOG_NOTICE, l($this->t('edit'), 'poll/term/' . $term->id() . '/edit'));
//        break;
//      case SAVED_UPDATED:
//        drupal_set_message($this->t('Updated term %term.', array('%term' => $term->label())));
//        watchdog('poll', 'Updated term %term.', array('%term' => $term->label()), WATCHDOG_NOTICE, l($this->t('edit'), 'poll/term/' . $term->id() . '/edit'));
//        // Clear the page and block caches to avoid stale data.
//        Cache::invalidateTags(array('content' => TRUE));
//        break;
//    }
//
//    $current_parent_count = count($form_state['values']['parent']);
//    $previous_parent_count = count($form_state['poll']['parent']);
//    // Root doesn't count if it's the only parent.
//    if ($current_parent_count == 1 && isset($form_state['values']['parent'][0])) {
//      $current_parent_count = 0;
//      $form_state['values']['parent'] = array();
//    }
//
//    // If the number of parents has been reduced to one or none, do a check on the
//    // parents of every term in the vocabulary value.
//    if ($current_parent_count < $previous_parent_count && $current_parent_count < 2) {
//      poll_check_vocabulary_hierarchy($form_state['poll']['vocabulary'], $form_state['values']);
//    }
//    // If we've increased the number of parents and this is a single or flat
//    // hierarchy, update the vocabulary immediately.
//    elseif ($current_parent_count > $previous_parent_count && $form_state['poll']['vocabulary']->hierarchy != TAXONOMY_HIERARCHY_MULTIPLE) {
//      $form_state['poll']['vocabulary']->hierarchy = $current_parent_count == 1 ? TAXONOMY_HIERARCHY_SINGLE : TAXONOMY_HIERARCHY_MULTIPLE;
//      $form_state['poll']['vocabulary']->save();
//    }
//
//    $form_state['values']['tid'] = $term->id();
//    $form_state['tid'] = $term->id();
//  }
//
//  /**
//   * {@inheritdoc}
//   */
//  public function delete(array $form, array &$form_state) {
//    $destination = array();
//    if (isset($_GET['destination'])) {
//      $destination = drupal_get_destination();
//      unset($_GET['destination']);
//    }
//    $form_state['redirect'] = array('poll/term/' . $this->entity->id() . '/delete', array('query' => $destination));
//  }

}
