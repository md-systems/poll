<?php

/**
 * @file
 * Definition of Drupal\poll\Plugin\field\widget\ChoiceVoteDefaultWidget.
 */

namespace Drupal\poll\Plugin\field\widget;

use Drupal\field\Annotation\FieldWidget;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Entity\Field\FieldInterface;
use Drupal\field\Plugin\Type\Widget\WidgetBase;
use Drupal;


/**
 * Plugin implementation of the 'poll_choice_default' widget.
 *
 * @FieldWidget(
 *   id = "poll_choice_default",
 *   module = "poll",
 *   label = @Translation("Poll choice"),
 *   field_types = {
 *     "poll_choice"
 *   }
 * )
 */
class PollChoiceDefaultWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldInterface $items, $delta, array $element, $langcode, array &$form, array &$form_state) {
    $element['choice'] = array(
      '#type' => 'textfield',
      '#placeholder' => t('Choice'),
      '#empty_value' => '',
      '#default_value' => isset($items[$delta]->choice) ? $items[$delta]->choice : '',
      '#prefix' => '<div class="container-inline">',
    );
    $element['vote'] = array(
      '#type' => 'number',
      '#placeholder' => t('Vote'),
      '#empty_value' => '',
      '#default_value' => isset($items[$delta]->vote) ? $items[$delta]->vote : '',
      '#min' => 0,
      '#suffix' => '</div>',
    );
    return $element;
  }
}
