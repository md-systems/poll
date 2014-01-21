<?php

/**
 * @file
 * Definition of Drupal\poll\PollStorageController.
 */

namespace Drupal\poll;

use Drupal\Component\Uuid\UuidInterface;
use Drupal\Core\Entity\EntityInterface;
//use Drupal\Core\Password\PasswordInterface;
use Drupal\Core\Database\Connection;
use Drupal\field\FieldInfo;
use Drupal\user\UserDataInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\FieldableDatabaseStorageController;

/**
 * Defines a Controller class for poll terms.
 */
class PollStorageController extends FieldableDatabaseStorageController implements PollStorageControllerInterface {

  /**
   * Provides the user data service object.
   *
   * @var \Drupal\user\UserDataInterface
   */
  protected $userData;

  /**
   * Constructs a new UserStorageController object.
   *
   * @param string $entity_type
   *  The entity type for which the instance is created.
   * @param array $entity_info
   *   An array of entity info for the entity type.
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection to be used.
   * @param \Drupal\field\FieldInfo $field_info
   *   The field info service.
   * @param \Drupal\Component\Uuid\UuidInterface $uuid_service
   *   The UUID Service.
   * @param \Drupal\Core\Password\PasswordInterface $password
   *   The password hashing service.
   * @param \Drupal\user\UserDataInterface $user_data
   *   The user data service.
   */
  public function __construct($entity_type, $entity_info, Connection $database, FieldInfo $field_info, UuidInterface $uuid_service, UserDataInterface $user_data) {
    parent::__construct($entity_type, $entity_info, $database, $field_info, $uuid_service);

    $this->userData = $user_data;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, $entity_type, array $entity_info) {
    return new static(
      $entity_type,
      $entity_info,
      $container->get('database'),
      $container->get('field.info'),
      $container->get('uuid'),
//      $container->get('password'),
      $container->get('user.data')
    );
  }

  public function deletePoll($poll) {

  }


  /**
   * {@inheritdoc}
   */
//  public function save(EntityInterface $entity) {
//    if (!$entity->id()) {
//      $entity->uid->value = $this->database->nextId($this->database->query('SELECT MAX(id) FROM {poll}')->fetchField());
//      $entity->enforceIsNew();
//    }
//    parent::save($entity);
//  }

  /**
   * TODO:
   */
  public function updatePoll(EntityInterface $poll) {

  }





  /**
   * TODO: doesn't belong here
   *
   * {@inheritdoc}
   */
  public function getTranslationFromContext(EntityInterface $entity, $langcode = NULL, $context = array()) {
    $translation = $entity;

    if ($entity instanceof TranslatableInterface) {
      if (empty($langcode)) {
        $langcode = $this->languageManager->getLanguage(Language::TYPE_CONTENT)->id;
      }

      // Retrieve language fallback candidates to perform the entity language
      // negotiation.
      $context['data'] = $entity;
      $context += array('operation' => 'entity_view');
      $candidates = $this->languageManager->getFallbackCandidates($langcode, $context);

      // Ensure the default language has the proper language code.
      $default_language = $entity->getUntranslated()->language();
      $candidates[$default_language->id] = Language::LANGCODE_DEFAULT;

      // Return the most fitting entity translation.
      foreach ($candidates as $candidate) {
        if ($entity->hasTranslation($candidate)) {
          $translation = $entity->getTranslation($candidate);
          break;
        }
      }
    }

    return $translation;
  }

}
