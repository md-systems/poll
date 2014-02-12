<?php

/**
 * @file
 * Definition of \Drupal\poll\Form\PollViewFormController.
 */

namespace Drupal\poll;

use Drupal\Component\Uuid\Uuid;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Language\Language;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\ContentEntityFormController;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Language\LanguageManager;
use Drupal\Component\Utility\String;
use Drupal;


/**
 * Base for controller for poll term edit forms.
 */
class PollViewFormController extends ContentEntityFormController {


  /**
   * {@inheritdoc}
   */
  public function form(array $form, array &$form_state) {
    drupal_set_title($this->entity->getLabel());

    if ($this->showResults($this->entity, $form_state)) {
      $form['results']['#markup'] = $this->poll_view_results($this->entity);
    }
    else {
      $options = $this->entity->getOptions();
      if ($options) {
        $form['choice'] = array(
          '#type' => 'radios',
          '#title' => t('Choices'),
          '#title_display' => 'invisible',
          '#options' => $options,
        );
      }
      $form['#theme'] = 'poll_vote';
      $form['#entity'] = $this->entity;
      // Set form caching because we could have multiple of these forms on
      // the same page, and we want to ensure the right one gets picked.
      $form_state['cache'] = TRUE;
      // Set a flag to hide results which will be removed if we want to view
      // results when the form is rebuilt.
      $form_state['input']['show_results'] = FALSE;
    }


    return $form;
  }

  /**
   * Returns the action form element for the current entity form.
   */
  protected function actions(array $form, array &$form_state) {
    $actions = parent::actions($form, $form_state);
    if (count($actions)) {
      foreach ($actions as $name => $action) {
        if ($name != 'submit') {
          unset($actions[$name]);
        }
      }
    }

    if ($this->showResults($this->entity, $form_state)) {
      // Remove all actions.
      $actions = array();
      // Allow user to cancel their vote.
      if ($this->entity->hasUserVoted() && $this->entity->cancel_vote_allow->value) {
        $actions['#type'] = 'actions';
        $actions['cancel']['#type'] = 'submit';
        $actions['cancel']['#button_type'] = 'primary';
        $actions['cancel']['#value'] = $this->t('Cancel vote');
        $actions['cancel']['#submit'] = array(array($this, 'cancel'));
        $actions['cancel']['#weight'] = '0';
      }
      if (!$this->entity->hasUserVoted()) {
        $actions['#type'] = 'actions';
        $actions['back']['#type'] = 'submit';
        $actions['back']['#button_type'] = 'primary';
        $actions['back']['#value'] = $this->t('View poll');
        $actions['back']['#submit'] = array(array($this, 'back'));
        $actions['back']['#weight'] = '0';
      }
    }
    else {
      $actions['#type'] = 'actions';
      $actions['submit']['#type'] = 'submit';
      $actions['submit']['#button_type'] = 'primary';
      $actions['submit']['#value'] = $this->t('Vote');
      $actions['submit']['#validate'] = array(array($this, 'validate'));
      $actions['submit']['#weight'] = '0';

      // view results before voting
      if ($this->entity->result_vote_allow->value) {
        $actions['result']['#type'] = 'submit';
        $actions['result']['#button_type'] = 'primary';
        $actions['result']['#value'] = $this->t('View results');
        $actions['result']['#submit'] = array(array($this, 'result'));
        $actions['result']['#weight'] = '1';
      }
    }

    return $actions;
  }

  public function cancel(array $form, array &$form_state) {
    $poll_storage_controller = \Drupal::entityManager()
      ->getStorageController($this->entity->entityType());
    $poll_storage_controller->cancelVote($this->entity, NULL);

    drupal_set_message($this->t('Your vote has been cancelled.'));

    $uri = $this->entity->normaliseUri();
    $form_state['redirect'] = $uri['path'];
  }

  public function result(array $form, array &$form_state) {
    $form_state['input']['show_results'] = TRUE;
    $form_state['rebuild'] = TRUE;
  }

  public function back(array $form, array &$form_state) {
    $form_state['input']['show_results'] = FALSE;
    $form_state['rebuild'] = TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, array &$form_state) {
    $options = array();
    $options['chid'] = $form_state['values']['choice'];
    $options['uid'] = Drupal::currentUser()->id();
    $options['pid'] = $this->entity->id();
    $options['hostname'] = Drupal::request()->getClientIp();
    $options['timestamp'] = REQUEST_TIME;

    // save vote
    $poll_storage_controller = \Drupal::entityManager()
      ->getStorageController($this->entity->entityType());
    $status = $poll_storage_controller->saveVote($options);

    drupal_set_message($this->t('You vote has been recorded.'));

    $uri = $this->entity->normaliseUri();
    $form_state['redirect'] = $uri['path'];
  }

  /**
   * @inheritdoc
   */
  public function validate(array $form, array &$form_state) {
    if (!isset($form_state['values']['choice']) || $form_state['values']['choice'] == NULL) {
      drupal_set_title($this->entity->question->value);
      Drupal::formBuilder()
        ->setErrorByName('choice', $form_state, $this->t('Your vote could not be recorded because you did not select any of the choices.'));
    }
  }


  public function showResults(PollInterface $poll, $form_state) {
    switch (TRUE) {
      case (isset($form_state['input']) && isset($form_state['input']['show_results']) && $form_state['input']['show_results']):
        return TRUE;
      case ($poll->isClosed()):
        return TRUE;
      case (user_is_anonymous() && !$poll->anonymous_vote_allow->value):
        return TRUE;
      case ($poll->hasUserVoted()):
        return TRUE;
      default:
        return FALSE;
    }
  }

  public function showViewResults(array $form_state) {
    return (isset($form_state['input']) &&
      isset($form_state['input']['show_results']) &&
      !$form_state['input']['show_results'] &&
      $this->entity->result_vote_allow->value);
  }

  function poll_view_results($poll, $block = FALSE) {
    $total_votes = 0;
    foreach ($poll->votes as $vote) {
      $total_votes += $vote;
    }

    $options = $poll->getOptions();
    $poll_results = array();
    foreach ($poll->votes as $pid => $vote) {
      $percentage = round($vote * 100 / max($total_votes, 1));
      $display_votes = (!$block) ? ' (' . Drupal::translation()
          ->formatPlural($vote, '1 vote', '@count votes') . ')' : '';

      $poll_results[] = array(
        '#theme' => 'poll_meter',
        '#prefix' => '<dt class="choice-title">' . String::checkPlain($options[$pid]) . "</dt>\n" . '<dd class="choice-result">',
        '#suffix' => "</dd>\n",
        '#display_value' => t('!percentage%', array('!percentage' => $percentage)) . $display_votes,
        '#min' => 0,
        '#max' => $total_votes,
        '#value' => $vote,
        '#percentage' => $percentage,
        '#attributes' => array('class' => array('bar')),
      );
    }

    return theme('poll_results', array(
      'raw_title' => $poll->label(),
      'results' => drupal_render($poll_results),
      'votes' => $total_votes,
      'raw_links' => isset($poll->links) ? $poll->links : array(),
      'block' => $block,
      'nid' => $poll->pid,
      'vote' => isset($poll->vote) ? $poll->vote : NULL
    ));
  }


}
