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
 *       "add" = "Drupal\poll\PollFormController",
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
 *   },
 *   bundle_keys = {
 *     "bundle" = "bundle"
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


  public $anonymousVoteAllowed;

  public $cancelVoteAllowed;

  public $resultVoteAllowed;


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
  public function setAnonymousVoteAllowed($anonymousVoteAllowed) {
    $this->set('anonymous_vote_allow', $anonymousVoteAllowed);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setCancelVoteAllowed($cancelVoteAllowed) {
    $this->set('cancel_vote_allow', $cancelVoteAllowed);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setResultVoteAllowed($resultVoteAllowed) {
    $this->set('result_vote_allow', $resultVoteAllowed);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isAnonymousVoteAllowed() {
    return (bool) $this->get('anonymous_vote_allow')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function isCancelVoteAllowed() {
    return (bool) $this->get('cancel_vote_allow')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function isResultVoteAllowed() {
    return (bool) $this->get('result_vote_allow')->value;
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
    return (bool) $this->get('status')->value;
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
      'description' => t('The user ID of the node author.'),
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
    $properties['features'] = array(
      'label' => t('Features'),
      'description' => t('The properties of the poll.'),
      'type' => 'string_field',
      'settings' => array(
        'default_value' => '',
      ),
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
