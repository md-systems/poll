<?php

/**
 * @file
 * Definition of Drupal\poll\Plugin\field\FieldFormatter\PollChoiceDefaultFormatter.
 */

namespace Drupal\choice\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation of the 'poll_choice' formatter.
 *
 * @FieldFormatter(
 *   id = "choice_default",
 *   module = "choice",
 *   label = @Translation("Choice"),
 *   field_types = {
 *     "choice"
 *   }
 * )
 */
class ChoiceDefaultFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items) {
    $elements = array();
    foreach ($items as $delta => $item) {
      $elements[$delta] = array('#markup' => $item->choice);
    }
    return $elements;
  }

}
