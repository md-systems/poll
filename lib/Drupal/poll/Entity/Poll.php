<?php

/**
 * @file
 * Definition of Drupal\poll\Entity\Poll.
 */

namespace Drupal\poll\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Field\FieldDefinition;
use Drupal\Core\Entity\EntityStorageControllerInterface;
use Drupal\poll\PollInterface;
use Drupal\Component\Utility\String;

/**
 * Defines the poll entity class.
 *
 * @EntityType(
 *   id = "poll",
 *   label = @Translation("Poll"),
 *   controllers = {
 *    "access" = "Drupal\poll\PollAccessController",
 *     "list" = "Drupal\poll\PollListController",
 *     "storage" = "Drupal\poll\PollStorageController",
 *     "form" = {
 *      "default" = "Drupal\poll\PollFormController",
 *      "add" = "Drupal\poll\PollFormController",
 *      "edit" = "Drupal\poll\PollFormController",
 *      "delete" = "Drupal\poll\Form\PollDeleteForm",
 *      "view" = "Drupal\poll\PollViewFormController",
 *     },
 *   },
 *   base_table = "poll_poll",
 *   fieldable = TRUE,
 *   translatable = TRUE,
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "question",
 *     "uuid" = "uuid"
 *   }
 * )
 */
class Poll extends ContentEntityBase implements PollInterface {

  /**
   * The poll ID.
   *
   * @var string
   */
  public $id;

  /**
   * The User ID of poll author.
   *
   * @var string
   */
  public $uid;

  /**
   * The poll UUID.
   *
   * @var string
   */
  public $uuid;

  /**
   * Question for the poll.
   *
   * @var string
   */
  public $question;

  /**
   * The lnaguage code.
   *
   * @var string
   */
  public $langcode;

  /**
   * Are anonymous users allowed to vote.
   *
   * @var boolean
   */
  public $anonymous_vote_allow;

  /**
   * Are users allowed to cancel their vote.
   *
   * @var boolean
   */
  public $cancel_vote_allow;

  /**
   * Are users allowed to view results before voting.
   *
   * @var boolean
   */
  public $result_vote_allow;

  /**
   * @var integer
   */
  public $runtime;

  /**
   * Flag indicating whether the poll is active or not.
   *
   * @var boolean
   */
  public $status;

  /**
   * The time that the poll was created.
   *
   * @var \Drupal\Core\Field\FieldItemInterface
   */
  public $created;

  /**
   * The choice field values for this vote.
   *
   * @var
   */
  public $field_choice;


  /**
   * {@inheritdoc}
   */
  public function init() {
    parent::init();

    // We unset all defined properties, so magic getters apply.
    unset($this->id);
    unset($this->uid);
    unset($this->uuid);
    unset($this->question);
    unset($this->langcode);
    unset($this->anonymous_vote_allow);
    unset($this->cancel_vote_allow);
    unset($this->result_vote_allow);
    unset($this->runtime);
    unset($this->status);
    unset($this->created);
    unset($this->field_choice);
  }

  /**
   * Implements Drupal\Core\Entity\EntityInterface::id().
   */
  public function id() {
    return $this->get('id')->value;
  }

  /**
   * Implements Drupal\Core\Entity\EntityInterface::label().
   */
  public function label() {
    return $this->get('question')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getLabel() {
    return $this->getQuestion();
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->get('question')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setQuestion($question) {
    $this->set('question', $question);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setAnonymousVoteAllow($anonymousVoteAllow) {
    $this->set('anonymous_vote_allow', $anonymousVoteAllow);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setCancelVoteAllow($cancelVoteAllow) {
    $this->set('cancel_vote_allow', $cancelVoteAllow);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setResultVoteAllow($resultVoteAllow) {
    $this->set('result_vote_allow', $resultVoteAllow);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isAnonymousVoteAllow() {
    return $this->get('anonymous_vote_allow')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function isCancelVoteAllow() {
    return $this->get('cancel_vote_allow')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function isResultVoteAllow() {
    return $this->get('result_vote_allow')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getRuntime() {
    return $this->get('runtime')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setRuntime($runtime) {
    $this->set('runtime', $runtime);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isActive() {
    return $this->get('status')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setActive($active) {
    $this->set('status', $active ? POLL_PUBLISHED : POLL_NOT_PUBLISHED);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getAuthor() {
    return $this->get('uid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getAuthorId() {
    return $this->get('uid')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setAuthorId($uid) {
    $this->set('uid', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isClosed() {
    return $this->get('status')->value == 0;
  }

  /**
   * {@inheritdoc}
   */
  public function activate() {
    $this->get('status')->value = 1;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function close() {
    $this->get('status')->value = 0;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreated() {
    return $this->get('created')->value;
  }

  public function setCreated($created = NULL) {
    $this->set('created', isset($created) ? $created : REQUEST_TIME);
    return $this;
  }

  public function setFieldChoice($fieldChoice) {
    $this->set('field_choice', $fieldChoice);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setLangcode($langcode) {
    $this->set('langcode', $langcode);
    return $this;
  }

  /**
   * @todo: Where is this being used?
   *
   * {@inheritdoc}
   */
  public static function sort($a, $b) {
    return strcmp($a->getQuestion(), $b->getQuestion());
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions($entity_type) {
    $fields['id'] = FieldDefinition::create('integer')
      ->setLabel(t('Poll ID'))
      ->setDescription(t('The poll ID.'))
      ->setReadOnly(TRUE);

    $fields['uuid'] = FieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The poll UUID.'))
      ->setReadOnly(TRUE);

    $fields['uid'] = FieldDefinition::create('entity_reference')
      ->setLabel(t('User ID'))
      ->setDescription(t('The user ID of the poll author.'))
      ->setSettings(array(
        'target_type' => 'user',
        'default_value' => 0,
      ));

    $fields['question'] = FieldDefinition::create('text')
      ->setLabel(t('Question'))
      ->setDescription(t('The poll question.'))
      ->setRequired(TRUE)
      ->setTranslatable(TRUE)
      ->setSettings(array(
        'default_value' => '',
        'max_length' => 255,
        'text_processing' => 0,
      ));

    $fields['langcode'] = FieldDefinition::create('language')
      ->setLabel(t('Language code'))
      ->setDescription(t('The poll language code.'));

    $fields['anonymous_vote_allow'] = FieldDefinition::create('boolean')
      ->setLabel(t('Allow anonymous votes'))
      ->setDescription(t('A boolean indicating whether anonymous users are allowed to vote.'));

    $fields['cancel_vote_allow'] = FieldDefinition::create('boolean')
      ->setLabel(t('Allow cancel votes'))
      ->setDescription(t('A boolean indicating whether users may cancel their vote.'));

    $fields['result_vote_allow'] = FieldDefinition::create('boolean')
      ->setLabel(t('Allow view results'))
      ->setDescription(t('A boolean indicating whether users may see the results before voting.'));

    $fields['runtime'] = FieldDefinition::create('boolean')
      ->setLabel(t('Runtime'))
      ->setDescription(t('The number of seconds after creation during which the poll is active.'));

    $fields['status'] = FieldDefinition::create('boolean')
      ->setLabel(t('Status'))
      ->setDescription(t('A boolean indicating whether the poll is active.'));

    // @todo Convert to a "created" field in https://drupal.org/node/2145103.
    $fields['created'] = FieldDefinition::create('integer')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the poll was created.'));

    return $fields;
  }

  /**
   * @todo: Refactor - doesn't belong here.
   *
   * @return mixed
   */
  public function hasUserVoted() {
    $poll_storage_controller = \Drupal::entityManager()
      ->getStorageController($this->entityType());
    return $poll_storage_controller->getUserVote($this);
  }

  /**
   * Get all options for this poll.
   *
   * @return array
   */
  public function getOptions() {
    $options = array();
    if (count($this->field_choice)) {
      foreach ($this->field_choice as $option) {
        $options[$option->chid] = String::checkPlain($option->choice);
      }
    }
    return $options;
  }

  /**
   * Get the values of each vote option for this poll.
   *
   * @return array
   */
  public function getOptionValues() {
    $options = array();
    if (count($this->field_choice)) {
      foreach ($this->field_choice as $option) {
        $options[$option->chid] = $option->vote;
      }
    }
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public static function postLoad(EntityStorageControllerInterface $storage_controller, array &$entities) {
    foreach ($entities as $entity) {
      $entity->votes = $storage_controller->getVotes($entity);
    }
  }

  /**
   * Remove 'entity/' from the generted uri for this entity.
   *
   * @return mixed
   */
  public function normaliseUri() {
    $uri = $this->uri();
    return str_replace('entity/', '', $uri);
  }

}