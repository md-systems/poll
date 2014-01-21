<?php

/**
 * @file
 * Definition of Drupal\poll\PollFormController.
 */

namespace Drupal\poll;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Entity\ContentEntityFormController;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Language\Language;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Base for controller for poll term edit forms.
 */
class PollFormController extends ContentEntityFormController {

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManager
   */
  protected $languageManager;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * Constructs a new EntityFormController object.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   * @param \Drupal\Core\Language\LanguageManager $language_manager
   *   The language manager.
   */
  public function __construct(EntityManagerInterface $entity_manager, LanguageManager $language_manager, ConfigFactory $config_factory) {
    parent::__construct($entity_manager);
    $this->languageManager = $language_manager;
    $this->configFactory = $config_factory;
  }


  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager'),
      $container->get('language_manager'),
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, array &$form_state) {
    $poll = $this->entity;

    $form['question'] = array(
      '#type' => 'textfield',
      '#title' => t('Question'),
      '#default_value' => ($poll->isNew()) ? '' : $poll->getQuestion(),
      '#maxlength' => 255,
      '#required' => TRUE,
      '#weight' => -1,
    );

    // Poll attributes
    $duration = array(
      // 1-6 days.
      86400,
      2 * 86400,
      3 * 86400,
      4 * 86400,
      5 * 86400,
      6 * 86400,
      // 1-3 weeks (7 days).
      604800,
      2 * 604800,
      3 * 604800,
      // 1-3,6,9 months (30 days).
      2592000,
      2 * 2592000,
      3 * 2592000,
      6 * 2592000,
      9 * 2592000,
      // 1 year (365 days).
      31536000,
    );
    $duration = array(0 => t('Unlimited')) + drupal_map_assoc($duration, 'format_interval');

    $form['runtime'] = array(
      '#type' => 'select',
      '#title' => t('Poll duration'),
      '#default_value' => ($poll->isNew()) ? POLL_PUBLISHED : $poll->getRuntime(),
      '#options' => $duration,
      '#description' => t('After this period, the poll will be closed automatically.'),
      '#weight' => 0,
    );

    $form['anonymous_vote_allow'] = array(
      '#type' => 'radios',
      '#title' => $this->t('Anonymous votes allowed'),
      '#default_value' => ($poll->isNew()) ? 0 : $poll->isAnonymousVoteAllow(),
      '#options' => array($this->t('No'), $this->t('Yes')),
      '#weight' => 1,
    );
    $form['cancel_vote_allow'] = array(
      '#type' => 'radios',
      '#title' => $this->t('Cancel votes allowed'),
      '#default_value' => ($poll->isNew()) ? 0 : $poll->isCancelVoteAllow(),
      '#options' => array($this->t('No'), $this->t('Yes')),
      '#weight' => 2,
    );
    $form['result_vote_allow'] = array(
      '#type' => 'radios',
      '#title' => $this->t('View results allowed'),
      '#default_value' => ($poll->isNew()) ? 0 : $poll->isResultVoteAllow(),
      '#options' => array($this->t('No'), $this->t('Yes')),
      '#weight' => 3,
    );

    $form['status'] = array(
      '#type' => 'radios',
      '#title' => $this->t('Status'),
      '#default_value' => ($poll->isNew()) ? 1 : $poll->isActive(),
      '#options' => array($this->t('Closed'), $this->t('Active')),
      '#weight' => 4,
    );

    $form['langcode'] = array(
      '#title' => $this->t('Language'),
      '#type' => 'language_select',
      '#default_value' => $poll->language()->id,
      '#languages' => Language::STATE_ALL,
      '#weight' => 5,
    );

//    echo '<pre>'; var_dump($poll); echo '</pre>';

    return parent::form($form, $form_state, $poll);
  }

  /**
   * {@inheritdoc}
   */
  public function buildEntity(array $form, array &$form_state) {
    global $user;

    $poll = parent::buildEntity($form, $form_state);

    $poll->setQuestion($form_state['values']['question']);
    $poll->setAnonymousVoteAllow($form_state['values']['anonymous_vote_allow']);
    $poll->setCancelVoteAllow($form_state['values']['cancel_vote_allow']);
    $poll->setResultVoteAllow($form_state['values']['result_vote_allow']);
    $poll->setCreated(REQUEST_TIME);
    $poll->setRuntime($form_state['values']['runtime']);
    $poll->setAuthorId($user->id());
    ((bool) $form_state['values']['status']) ? $poll->activate() : $poll->close();

    return $poll;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, array &$form_state) {
    $poll = $this->entity;


    $data = $poll;
    $string = check_plain(print_r($data, TRUE));
    $string = '<pre>' . $string . '</pre>';
    trigger_error($string);



    $poll->save();
    $form_state['redirect'] = 'admin/structure/poll';
  }

  /**
   * {@inheritdoc}
   */
  protected function actions(array $form, array &$form_state) {
    $element = parent::actions($form, $form_state);

//    // The user account being edited.
//    $account = $this->entity;
//
//    // The user doing the editing.
//    $user = $this->currentUser();
//    $element['delete']['#type'] = 'submit';
//    $element['delete']['#value'] = $this->t('Cancel account');
//    $element['delete']['#submit'] = array(array($this, 'editCancelSubmit'));
//    $element['delete']['#access'] = $account->id() > 1 && (($account->id() == $user->id() && $user->hasPermission('cancel account')) || $user->hasPermission('administer users'));

    return $element;
  }

  /**
   * Provides a submit handler for the 'Cancel account' button.
   */
  public function editCancelSubmit($form, &$form_state) {
    $destination = array();
    $query = $this->getRequest()->query;
//    if ($query->has('destination')) {
//      $destination = array('destination' => $query->get('destination'));
//      $query->remove('destination');
//    }
//    // We redirect from user/%/edit to user/%/cancel to make the tabs disappear.
//    $form_state['redirect_route'] = array(
//      'route_name' => 'user.cancel',
//      'route_parameters' => array('user' => $this->entity->id()),
//      'options' => array('query' => $destination),
//    );
  }

}
