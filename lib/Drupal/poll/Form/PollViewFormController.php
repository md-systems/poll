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









/**



function poll_vote($form, &$form_state) {
    $node = $form['#node'];
    $choice = $form_state['values']['choice'];

    $account = Drupal::request()->attributes->get('_account');
    db_insert('poll_vote')
    ->fields(array(
    'nid' => $node->nid,
    'chid' => $choice,
    'uid' => $account->id(),
    'hostname' => Drupal::request()->getClientIp(),
    'timestamp' => REQUEST_TIME,
    ))
    ->execute();

    // Add one to the votes.
    db_update('poll_choice')
    ->expression('chvotes', 'chvotes + 1')
    ->condition('chid', $choice)
    ->execute();

    cache_invalidate_tags(array('content' => TRUE));

    if (!$account->id()) {
    // The vote is recorded so the user gets the result view instead of the
    // voting form when viewing the poll. Saving a value in $_SESSION has the
    // convenient side effect of preventing the user from hitting the page
    // cache. When anonymous voting is allowed, the page cache should only
    // contain the voting form, not the results.
    $_SESSION['poll_vote'][$node->nid] = $choice;
    }

    drupal_set_message(t('Your vote was recorded.'));

    // Return the user to whatever page they voted from.
}










function poll_view($node, $view_mode) {
global $user;
$output = '';

if (!empty($node->allowvotes) && empty($node->show_results)) {
$node->content['poll_view_voting'] = drupal_get_form('poll_view_voting', $node);
}
else {
$node->content['poll_view_results'] = array('#markup' => poll_view_results($node, $view_mode));
}
return $node;
}



function poll_view_voting($form, &$form_state, $node, $block = FALSE) {
if ($node->choice) {
$list = array();
foreach ($node->choice as $i => $choice) {
$list[$i] = check_plain($choice['chtext']);
}
$form['choice'] = array(
'#type' => 'radios',
'#title' => t('Choices'),
'#title_display' => 'invisible',
'#default_value' => -1,
'#options' => $list,
);
}

$form['vote'] = array(
'#type' => 'submit',
'#value' => t('Vote'),
'#submit' => array('poll_vote'),
);

// Store the node so we can get to it in submit functions.
$form['#node'] = $node;
$form['#block'] = $block;

// Set form caching because we could have multiple of these forms on
// the same page, and we want to ensure the right one gets picked.
$form_state['cache'] = TRUE;

// Provide a more cleanly named voting form theme.
$form['#theme'] = 'poll_vote';
return $form;
}


function template_preprocess_poll_vote(&$variables) {
$form = $variables['form'];
$variables['choice'] = drupal_render($form['choice']);
$variables['title'] = check_plain($form['#node']->title);
$variables['vote'] = drupal_render($form['vote']);
$variables['rest'] = drupal_render_children($form);
$variables['block'] = $form['#block'];
if ($variables['block']) {
$variables['theme_hook_suggestions'][] = 'poll_vote__block';
}
}

function poll_view_results($node, $view_mode, $block = FALSE) {
// Make sure that choices are ordered by their weight.
uasort($node->choice, 'drupal_sort_weight');

// Count the votes and find the maximum.
$total_votes = 0;
$max_votes = 0;
foreach ($node->choice as $choice) {
if (isset($choice['chvotes'])) {
$total_votes += $choice['chvotes'];
$max_votes = max($max_votes, $choice['chvotes']);
}
}

$poll_results = '';
foreach ($node->choice as $i => $choice) {
if (!empty($choice['chtext'])) {
$chvotes = isset($choice['chvotes']) ? $choice['chvotes'] : NULL;
$poll_results .= theme('poll_bar', array('title' => $choice['chtext'], 'votes' => $chvotes, 'total_votes' => $total_votes, 'vote' => isset($node->vote) && $node->vote == $i, 'block' => $block));
}
}

return theme('poll_results', array('raw_title' => $node->title, 'results' => $poll_results, 'votes' => $total_votes, 'raw_links' => isset($node->links) ? $node->links : array(), 'block' => $block, 'nid' => $node->nid, 'vote' => isset($node->vote) ? $node->vote : NULL));
}



/**
 * Preprocess the poll_results theme hook.
 *
 * Inputs: $raw_title, $results, $votes, $raw_links, $block, $nid, $vote. The
 * $raw_* inputs to this are naturally unsafe; often safe versions are
 * made to simply overwrite the raw version, but in this case it seems likely
 * that the title and the links may be overridden by the theme layer, so they
 * are left in with a different name for that purpose.
 *
 * @see poll-results.tpl.php
 * @see poll-results--block.tpl.php
 */
/**
function template_preprocess_poll_results(&$variables) {
$variables['links'] = theme('links__poll_results', array('links' => $variables['raw_links']));
if (isset($variables['vote']) && $variables['vote'] > -1 && user_access('cancel own vote')) {
$elements = drupal_get_form('poll_cancel_form', $variables['nid']);
$variables['cancel_form'] = drupal_render($elements);
}
$variables['title'] = check_plain($variables['raw_title']);

if ($variables['block']) {
$variables['theme_hook_suggestions'][] = 'poll_results__block';
}
}

 */

/**
 * Preprocess the poll_bar theme hook.
 *
 * Inputs: $title, $votes, $total_votes, $voted, $block
 *
 * @see poll-bar.tpl.php
 * @see poll-bar--block.tpl.php
 * @see theme_poll_bar()
 */
/**
function template_preprocess_poll_bar(&$variables) {
if ($variables['block']) {
$variables['theme_hook_suggestions'][] = 'poll_bar__block';
}
$variables['title'] = check_plain($variables['title']);
$variables['percentage'] = round($variables['votes'] * 100 / max($variables['total_votes'], 1));
}

 */

