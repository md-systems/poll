<?php

/**
 * @file
 * Contains \Drupal\poll\Plugin\field\field_type\VoteChoiceItem.
 */

namespace Drupal\poll\Plugin\Field\FieldType;

use Drupal\Core\Field\ConfigFieldItemBase;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\field\FieldInterface;
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

  const POLL_CHOICE_MAX_LENGTH = 255;
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
  public static function schema(FieldInterface $field) {
    return array(
      'columns' => array(
        'choice' => array(
          'type' => 'varchar',
          'length' => 255,
          'not null' => FALSE,
        ),
        'vote' => array(
          'type' => 'int',
          'unsigned' => TRUE,
          'not null' => FALSE,
        ),
      ),
      'indexes' => array(
        'choice' => array('choice'),
        'vote' => array('vote'),
      ),
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
    $constraint_manager = \Drupal::typedData()->getValidationConstraintManager();
    $constraints = parent::getConstraints();
    $constraints[] = $constraint_manager->create('ComplexData', array(
      'choice' => array(
        'Length' => array(
          'max' => static::POLL_CHOICE_MAX_LENGTH,
          'maxMessage' => t('%name: the choice field may not be longer than @max characters.', array('%name' => $this->getFieldDefinition()->getLabel(), '@max' => static::POLL_CHOICE_MAX_LENGTH
          )),
        )
      ),
    ));
    return $constraints;
  }

}
