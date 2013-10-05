<?php

/**
 * @file
 * Definition of Drupal\poll\Entity\Poll.
 */

namespace Drupal\poll\Entity;

// this?
use Drupal\Core\Entity\EntityNG;

// or this?
use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\EntityStorageControllerInterface;
use Drupal\Core\Entity\Annotation\EntityType;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Language\Language;

use Drupal\Core\Session\AccountInterface;

use Drupal\poll\PollInterface;

/**
 * Defines the poll entity class.
 *
 * @EntityType(
 *   id = "poll",
 *   label = @Translation("Poll"),
 *   bundle_label = @Translation("Poll"),
 *   module = "poll",
 *   controllers = {
 *     "list" = "Drupal\poll\PollListController",
 *     "storage" = "Drupal\poll\PollStorageController",
 *     "render" = "Drupal\poll\PollRenderController",
 *     "access" = "Drupal\poll\PollAccessController",
 *     "form" = {
 *      "default" = "Drupal\poll\PollFormController",
 *      "add" = "Drupal\poll\PollFormController",
 *      "edit" = "Drupal\poll\PollFormController",
 *     },
 *     "translation" = "Drupal\poll\PollTranslationController"
 *   },
 *   base_table = "poll",
 *   uri_callback = "poll_uri",
 *   route_base_path = "admin/structure/types/manage/{bundle}",
 *   fieldable = TRUE,
 *   translatable = TRUE,
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid"
 *   }
 * )
 */
class Poll extends EntityNG implements PollInterface {

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


  public $anonymous_vote_allow;

  public $cancel_vote_allow;

  public $result_vote_allow;

  /**
   * @var integer
   */
  public $runtime;

  /**
   * Flag indicating whether the poll is active or not.
   * @var boolean
   */
  public $status;

  /**
   * The time that the poll was created.
   *
   * @var \Drupal\Core\Entity\Field\FieldInterface
   */
  public $created;


  public $field_choice;


  /**
   * Overrides Drupal\Core\Entity\EntityNG::init().
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
   * {@inheritdoc}
   */
  public static function sort($a, $b) {

    // Sort by label.
    return strcmp($a->getQuestion(), $b->getQuestion());
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions($entity_type) {
    $properties['id'] = array(
      'label' => t('Poll ID'),
      'description' => t('The poll ID.'),
      'type' => 'integer_field',
      'read-only' => TRUE,
    );
    $properties['uid'] = array(
      'label' => t('User ID'),
      'description' => t('The user ID of the poll author.'),
      'type' => 'entity_reference_field',
      'settings' => array(
        'target_type' => 'user',
        'default_value' => 0,
      ),
    );
    $properties['uuid'] = array(
      'label' => t('UUID'),
      'description' => t('The poll UUID.'),
      'type' => 'uuid_field',
      'read-only' => TRUE,
    );
    $properties['question'] = array(
      'label' => t('Question'),
      'description' => t('The poll question.'),
      'type' => 'string_field',
      'required' => TRUE,
      'settings' => array(
        'default_value' => '',
      ),
    );
    $properties['langcode'] = array(
      'label' => t('Language code'),
      'description' => t('The poll language code.'),
      'type' => 'language_field',
    );
    $properties['anonymous_vote_allow'] = array(
      'label' => t('Anonymous votes allowed'),
      'description' => t('A boolean indicating whether anonymous users are allowed to vote.'),
      'type' => 'boolean_field',
    );
    $properties['cancel_vote_allow'] = array(
      'label' => t('Cancel votes allowed'),
      'description' => t('A boolean indicating whether users may cancel their vote.'),
      'type' => 'boolean_field',
    );
    $properties['result_vote_allow'] = array(
      'label' => t('View results allowed'),
      'description' => t('A boolean indicating whether users may see the results before voting.'),
      'type' => 'boolean_field',
    );
    $properties['runtime'] = array(
      'label' => t('Runtime'),
      'description' => t('The number of seconds after creation during which the poll is active'),
      'type' => 'integer_field',
    );
    $properties['status'] = array(
      'label' => t('Status'),
      'description' => t('A boolean indicating whether the poll is active.'),
      'type' => 'boolean_field',
    );
    $properties['created'] = array(
      'label' => t('Created'),
      'description' => t('The time that the poll was created.'),
      'type' => 'integer_field',
      'settings' => array(
        'default_value' => '0',
      ),
    );

    return $properties;
  }

}
