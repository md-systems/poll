<?php

/**
 * @file
 * Contains \Drupal\poll\Plugin\field\field_type\VoteChoiceItem.
 */

namespace Drupal\poll\Plugin\field\field_type;

use Drupal\Core\Entity\Annotation\FieldType;
use Drupal\Core\Annotation\Translation;
use Drupal\field\Plugin\Type\FieldType\ConfigFieldItemBase;
use Drupal\field\FieldInterface;

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
      static::$propertyDefinitions['choice'] = array(
        'type' => 'string',
        'label' => t('Choice'),
      );
      static::$propertyDefinitions['vote'] = array(
        'type' => 'integer',
        'label' => t('Vote'),
      );
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
    $value = $this->field_choice;
    return $value === NULL || count($value) === 0;
  }

  /**
   * {@inheritdoc}
   */
  public function getConstraints() {
    $constraint_manager = \Drupal::typedData()->getValidationConstraintManager();
    $constraints = parent::getConstraints();

    $max_length = 255;
    $constraints[] = $constraint_manager->create('ComplexData', array(
      'choice' => array(
        'Length' => array(
          'max' => $max_length,
          'maxMessage' => t('%name: the choice field may not be longer than @max characters.', array('%name' => $this->getFieldDefinition()->getFieldLabel(), '@max' => $max_length)),
        )
      ),
    ));

    // TODO: vote can not be negative/ >=0

    return $constraints;
  }

}
