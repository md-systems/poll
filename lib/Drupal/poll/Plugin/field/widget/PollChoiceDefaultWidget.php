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
    $element['#attributes']['class'][] = 'container-inline';

    $element['parent'] = array(
      '#prefix' => '<div class="container-inline">',
      '#suffix' => '</div>',
    );

    $element['parent']['choice'] = array(
      '#type' => 'textfield',
      //'#title' => t('Choice'),
      '#placeholder' => t('Choice'),
      '#empty_value' => '',
      '#default_value' => isset($items[$delta]->choice) ? $items[$delta]->choice : NULL,
    );
    $element['parent']['vote'] = array(
      '#type' => 'number',
      //'#title' => t('Vote'),
      '#placeholder' => t('Vote'),
      '#empty_value' => '',
      '#default_value' => isset($items[$delta]->vote) ? $items[$delta]->vote : NULL,
      '#min' => 0,
    );

    return $element;
  }


}
