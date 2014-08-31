<?php

/**
 * @file
 * Contains \Drupal\ban\Form\BanAdmin.
 */

namespace Drupal\poll\Form;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\poll\PollInterface;
use Drupal\Component\Utility\String;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityInterface;

/**
 * Displays banned IP addresses.
 */
class PollViewForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'poll_view_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state, $poll = NULL) {
    // Add the poll to the form.
    $form['poll']['#type'] = 'value';
    $form['poll']['#value'] = $poll;

    if ($this->showResults($poll, $form_state)) {
      $form['results'] = $this->showPollResults($poll);
    }
    else {
      $options = $poll->getOptions();
      if ($options) {
        $form['choice'] = array(
          '#type' => 'radios',
          '#title' => t('Choices'),
          '#title_display' => 'invisible',
          '#options' => $options,
        );
      }
      // Add the poll to the form.
      $form['poll']['#type'] = 'value';
      $form['poll']['#value'] = $poll;

      $form['#theme'] = 'poll_vote';
      $form['#entity'] = $poll;
      // Set form caching because we could have multiple of these forms on
      // the same page, and we want to ensure the right one gets picked.
      $form_state['cache'] = TRUE;
      // Set a flag to hide results which will be removed if we want to view
      // results when the form is rebuilt.
      $form_state['input']['show_results'] = FALSE;
    }

    $form['actions'] = $this->actions($form, $form_state, $poll);

    $form['#cache'] = array(
      'tags' => $poll->getCacheTag(),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Leaving empty
    $this->save($form, $form_state);
  }

  public function showResults(PollInterface $poll, FormStateInterface $form_state) {
    $account = $this->currentUser();
    switch (TRUE) {
      // The "View results" button, when available, has been clicked.
      case (isset($form_state['input']) && isset($form_state['input']['show_results']) && $form_state['input']['show_results']):
        return TRUE;
      // The poll is closed.
      case ($poll->isClosed()):
        return TRUE;
      // Anonymous user is trying to view a poll they aren't allowed to vote in.
      case ($account->isAnonymous() && !$poll->getAnonymousVoteAllow()):
        return TRUE;
      // The user has already voted.
      case ($poll->hasUserVoted()):
        return TRUE;
      default:
        return FALSE;
    }
  }

  protected function actions(array $form, FormStateInterface $form_state, $poll) {
    // Remove all actions.
    $actions = array();
    if ($this->showResults($poll, $form_state)) {
      // Allow user to cancel their vote.
      if ($this->isCancelAllowed($poll)) {
        $actions['#type'] = 'actions';
        $actions['cancel']['#type'] = 'submit';
        $actions['cancel']['#button_type'] = 'primary';
        $actions['cancel']['#value'] = t('Cancel vote');
        $actions['cancel']['#submit'] = array(array($this, 'cancel'));
        $actions['cancel']['#weight'] = '0';
      }
      if (!$poll->hasUserVoted()) {
        $actions['#type'] = 'actions';
        $actions['back']['#type'] = 'submit';
        $actions['back']['#button_type'] = 'primary';
        $actions['back']['#value'] = t('View poll');
        $actions['back']['#submit'] = array(array($this, 'back'));
        $actions['back']['#weight'] = '0';
      }
    }
    else {
      $actions['#type'] = 'actions';
      $actions['submit']['#type'] = 'submit';
      $actions['submit']['#button_type'] = 'primary';
      $actions['submit']['#value'] = t('Vote');
//      $actions['submit']['#submit'] = array(array($this, 'save'));
      $actions['submit']['#weight'] = '0';

      // view results before voting
      if ($poll->result_vote_allow->value) {
        $actions['result']['#type'] = 'submit';
        $actions['result']['#button_type'] = 'primary';
        $actions['result']['#value'] = t('View results');
        $actions['result']['#submit'] = array(array($this, 'result'));
        $actions['result']['#weight'] = '1';
      }
    }

    return $actions;
  }

  /**
   * Display a themed poll results.
   *
   * @param \Drupal\poll\PollInterface $poll
   * @param bool $block
   *
   * @return false|string
   */
  function showPollResults(PollInterface $poll, $block = FALSE) {
    $total_votes = 0;
    foreach ($poll->votes as $vote) {
      $total_votes += $vote;
    }

    $options = $poll->getOptions();
    $poll_results = array();
    foreach ($poll->votes as $pid => $vote) {
      $percentage = round($vote * 100 / max($total_votes, 1));
      $display_votes = (!$block) ? ' (' . \Drupal::translation()
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

    $output = array(
      '#theme' => 'poll_results',
      '#raw_title' => $poll->label(),
      '#results' => $poll_results,
      '#votes' => $total_votes,
      '#raw_links' => isset($poll->links) ? $poll->links : array(),
      '#block' => $block,
      '#pid' => $poll->id(),
      '#vote' => isset($poll->vote) ? $poll->vote : NULL,
    );

    return $output;
  }

  /**
   * Cancel vote submit function.
   *
   * @param array $form
   * @param array $form_state
   */
  public function cancel(array $form, FormStateInterface $form_state) {
    $form_state->setRedirect('poll.poll_vote_delete', array(
        'poll' => $form_state['values']['poll']->id(),
        'user' => \Drupal::currentUser()->id(),
    ));
  }

  /**
   * View vote results submit function.
   *
   * @param array $form
   * @param array $form_state
   */
  public function result(array $form, FormStateInterface $form_state) {
    $form_state['input']['show_results'] = TRUE;
    $form_state['rebuild'] = TRUE;
  }

  /**
   * Back to poll view submit function.
   *
   * @param array $form
   * @param array $form_state
   */
  public function back(array $form, FormStateInterface $form_state) {
    $form_state['input']['show_results'] = FALSE;
    $form_state['rebuild'] = TRUE;
  }

  /**
   * Save a user's vote submit function.
   *
   * @param array $form
   * @param array $form_state
   */
  public function save(array $form, FormStateInterface $form_state) {
    $options = array();
    $options['chid'] = $form_state->getValue('choice');
    $options['uid'] = $this->currentUser()->id();
    $options['pid'] = $form_state->getValue('poll')->id();
    $options['hostname'] = \Drupal::request()->getClientIp();
    $options['timestamp'] = REQUEST_TIME;
    // save vote
    $pollStorage = \Drupal::entityManager()->getStorage('poll');
    $pollStorage->saveVote($options);
    // @todo: confirm vote has been saved.
    drupal_set_message($this->t('Your vote has been recorded.'));

    Cache::invalidateTags($form_state->getValue('poll')->getCacheTag());

    if ($this->currentUser()->isAnonymous()) {
      // The vote is recorded so the user gets the result view instead of the
      // voting form when viewing the poll. Saving a value in $_SESSION has the
      // convenient side effect of preventing the user from hitting the page
      // cache. When anonymous voting is allowed, the page cache should only
      // contain the voting form, not the results.
      $_SESSION['poll_vote'][$form_state->getValue('poll')->id()] = $form_state->getValue('choice');
    }

    $form_state->setRedirectUrl($form_state->getValue('poll')->urlInfo());
  }

  /**
   * {@inheritdoc}
   *
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('op') == 'Vote') {
      if (!$form_state->hasValue('choice')) {
        $form_state->setErrorByName('choice', $this->t('Your vote could not be recorded because you did not select any of the choices.'));
      }
    }
  }

  /**
   * Checks if the current user is allowed to cancel on the given poll.
   * @param \Drupal\poll\PollInterface $poll
   *
   * @return bool
   *   TRUE if the user can can cancel.
   */
  protected function isCancelAllowed(PollInterface $poll) {
    // Allow access if the user has voted.
    return $poll->hasUserVoted()
      // And the poll allows to cancel votes.
      && $poll->getCancelVoteAllow()
      // And the user has the cancel own vote permission.
      && $this->currentUser()->hasPermission('cancel own vote')
      // And the user is authenticated or his session contains the voted flag.
      && (\Drupal::currentUser()->isAuthenticated() || !empty($_SESSION['poll_vote'][$poll->id()]));
  }


}
