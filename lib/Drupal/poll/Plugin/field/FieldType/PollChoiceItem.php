<?php

/**
 * @file
 * Contains \Drupal\poll\Plugin\field\FieldType\VoteChoiceItem.
 */

namespace Drupal\poll\Plugin\Field\FieldType;

use Drupal\Core\Field\ConfigFieldItemBase;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal;

/**
 * Plugin implementation of the 'poll_choice' field type.
 *
 * @FieldType(
 *   id = "poll_choice",
 *   label = @Translation("Poll choice"),
 *   description = @Translation("Stores the poll choice and initial value of votes for the choice."),
 *   default_widget = "poll_choice_default",
 *   default_formatter = "poll_choice_default"
 * )
 */
class PollChoiceItem extends ConfigFieldItemBase {

  const POLL_CHOICE_MAX_LENGTH = 512;
  /**
   * Definitions of the contained properties.
   *
   * @var array
   */
  static $propertyDefinitions;

  /**
   * {@inheritdoc}
   */
  public function getPropertyDefinitions() {
    if (!isset(static::$propertyDefinitions)) {
      static::$propertyDefinitions['chid'] = DataDefinition::create('integer')
        ->setLabel(t('Choice ID'));
      static::$propertyDefinitions['choice'] = DataDefinition::create('string')
        ->setLabel(t('Choice'));
      static::$propertyDefinitions['vote'] = DataDefinition::create('integer')
        ->setLabel(t('Vote'));
    }
    return static::$propertyDefinitions;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldDefinitionInterface $field_definition) {
    return array(
      'columns' => array(
        'chid' => array(
          'type' => 'serial',
          'unsigned' => TRUE,
          'not null' => TRUE,
        ),
        'choice' => array(
          'type' => 'varchar',
          'length' => 512,
          'not null' => TRUE,
        ),
        'vote' => array(
          'type' => 'int',
          'unsigned' => TRUE,
          'not null' => FALSE,
        ),
      ),
      'indexes' => array(
        'chid' => array('chid'),
      ),
      'primary key' => array('chid'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('choice')->getValue();
    return $value === NULL || $value === '';
  }

  /**
   * {@inheritdoc}
   */
  public function getConstraints() {
    $constraint_manager = \Drupal::typedDataManager()
      ->getValidationConstraintManager();
    $constraints = parent::getConstraints();
    $constraints[] = $constraint_manager->create('ComplexData', array(
      'choice' => array(
        'Length' => array(
          'max' => static::POLL_CHOICE_MAX_LENGTH,
          'maxMessage' => t('%name: the choice field may not be longer than @max characters.', array(
            '%name' => $this->getFieldDefinition()->getLabel(),
            '@max' => static::POLL_CHOICE_MAX_LENGTH
          )),
        )
      ),
    ));
    return $constraints;
  }

}
