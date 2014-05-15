<?php

/**
 * @file
 * Definition of Drupal\poll\Plugin\field\FieldWidget\PollChoiceDefaultWidget.
 */

namespace Drupal\choice\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;


/**
 * Plugin implementation of the 'choice_default' widget.
 *
 * @FieldWidget(
 *   id = "choice_default",
 *   module = "choice",
 *   label = @Translation("Choice"),
 *   field_types = {
 *     "choice"
 *   }
 * )
 */
class ChoiceDefaultWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, array &$form_state) {
    $element['chid'] = array(
      '#type' => 'value',
      '#value' => $items[$delta]->chid,
    );
    $element['choice'] = array(
      '#type' => 'textfield',
      '#placeholder' => t('Choice'), // @todo:
      '#empty_value' => '',
      '#default_value' => isset($items[$delta]->choice) ? $items[$delta]->choice : '',
      '#prefix' => '<div class="container-inline">',
    );
    $element['vote'] = array(
      '#type' => 'number',
      '#placeholder' => t('Vote'), // @todo:
      '#empty_value' => '',
      '#default_value' => isset($items[$delta]->vote) ? $items[$delta]->vote : '',
      '#min' => 0,
      '#suffix' => '</div>',
    );
    return $element;
  }
}
