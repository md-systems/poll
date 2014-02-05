<?php

/**
 * @file
 * Definition of \Drupal\poll\Form\PollViewFormController.
 */

namespace Drupal\poll\Form;

use Drupal\Component\Uuid\Uuid;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Language\Language;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\ContentEntityFormController;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Language\LanguageManager;
use Drupal;


/**
 * Base for controller for poll term edit forms.
 */
class PollViewFormController extends ContentEntityFormController {


  /**
   * {@inheritdoc}
   */
  public function form(array $form, array &$form_state) {
    // user - first visit
    // user - voted - second visit
    // can user see vote before voting?

    $account = Drupal::request()->attributes->get('_account');

    drupal_set_title($this->entity->getLabel());

    $form = array();
    if (count($this->entity->field_choice)) {
      $list = array();
      foreach ($this->entity->field_choice as $i => $option) {
        $list[$i] = check_plain($option->choice);
      }

      shuffle($list);
      $form['choice'] = array(
        '#type' => 'radios',
        '#title' => t('Choices'),
        '#title_display' => 'invisible',
        '#options' => $list,
      );
    }

    // Store the node so we can get to it in submit functions.
    $form['#node'] = $this->entity;

    // Set form caching because we could have multiple of these forms on
    // the same page, and we want to ensure the right one gets picked.
    $form_state['cache'] = TRUE;

    // Provide a more cleanly named voting form theme.
    $form['#theme'] = 'poll_vote';

    return $form;

  }

  /**
   * Returns the action form element for the current entity form.
   */
  protected function actionsElement(array $form, array &$form_state) {
    $elements = $this->actions($form, $form_state);
    if (count($elements)) {
      foreach ($elements as $name => $element) {
        if ($name != 'submit') {
          unset($elements[$name]);
        }
      }
    }

    $elements['submit']['#type'] = 'submit';
    $elements['submit']['#button_type'] = 'primary';
    $elements['submit']['#value'] = t('Vote');
    $elements['submit']['#weight'] = '0';
    $elements['#type'] = 'actions';

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, array &$form_state) {

    $account = Drupal::request()->attributes->get('_account');

    $data = $form['choice']['#options'][$form_state['values']['choice']];
    $string = check_plain(print_r($data, TRUE));
    $string = '<pre>' . $string . '</pre>';
    trigger_error($string);

    $form_state['redirect'] = 'admin/structure/poll';
  }

  /**
   * @inheritdoc
   */
  public function validate(array $form, array &$form_state) {
    if (!isset($form_state['values']['choice']) || $form_state['values']['choice'] == NULL) {
      drupal_set_title($form['#node']->question->value);
      form_set_error( 'choice', t('Your vote could not be recorded because you did not select any of the choices.'));
    }
  }

  // TODO: refactor to a common class
  public function hasUserVoted($entity) {
    $account = Drupal::request()->attributes->get('_account');
    return FALSE;
  }

}
