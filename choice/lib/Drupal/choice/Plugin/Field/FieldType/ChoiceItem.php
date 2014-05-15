<?php

/**
 * @file
 * Contains \Drupal\poll\Plugin\field\FieldType\PollChoiceItem.
 */

namespace Drupal\choice\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;


/**
 * Plugin implementation of the 'poll_choice' field type.
 *
 * @FieldType(
 *   id = "choice",
 *   label = @Translation("Choice"),
 *   description = @Translation("Stores the choice and initial value for the choice."),
 *   default_widget = "choice_default",
 *   default_formatter = "choice_default"
 * )
 */
class ChoiceItem extends FieldItemBase {

  const CHOICE_MAX_LENGTH = 512;

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['chid'] = DataDefinition::create('integer')
      ->setLabel(t('Choice ID'));
    $properties['choice'] = DataDefinition::create('string')
      ->setLabel(t('Choice'));
    $properties['vote'] = DataDefinition::create('integer')
      ->setLabel(t('Vote'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
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
          'max' => static::CHOICE_MAX_LENGTH,
          'maxMessage' => t('%name: the choice field may not be longer than @max characters.', array(
            '%name' => $this->getFieldDefinition()->getLabel(),
            '@max' => static::CHOICE_MAX_LENGTH
          )),
        )
      ),
    ));
    return $constraints;
  }

}
