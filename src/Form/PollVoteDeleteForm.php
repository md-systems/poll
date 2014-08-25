<?php

/**
 * @file
 * Contains \Drupal\poll\Form\PollVoteDeleteForm.
 */

namespace Drupal\poll\Form;

use Drupal\Core\Entity\ContentEntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a form for deleting a vote.
 */
class PollVoteDeleteForm extends ContentEntityConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete this vote for %poll', array('%poll' => $this->entity->label()));
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return $this->entity->urlInfo();
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Always provide entity id in the same form key as in the entity edit form.
    // @todo: arg(4)
    $form['uid'] = array('#type' => 'value', '#value' => arg(4));
    $form = parent::buildForm($form, $form_state);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submit(array $form, FormStateInterface $form_state) {
    $account = entity_load('user', $form_state->getValue('uid'));
    $pollStorage = \Drupal::entityManager()->getStorage($this->entity->getId());
    $pollStorage->cancelVote($this->entity, $account);
    watchdog('poll', '%user\'s vote in Poll #%poll deleted.', array(
      '%user' => $account->id(),
      '%poll' => $this->entity->id()
    ));
    drupal_set_message($this->t('Your vote was cancelled.'));
    // Display the original poll.
    $form_state->setRedirect('poll.poll_view', array('poll' => $this->entity->id()));
  }
}
