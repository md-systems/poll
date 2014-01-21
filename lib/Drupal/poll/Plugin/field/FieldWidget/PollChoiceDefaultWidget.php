<?php

/**
 * @file
 * Definition of Drupal\poll\Plugin\field\widget\ChoiceVoteDefaultWidget.
 */

namespace Drupal\poll\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
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
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, array &$form_state) {
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
