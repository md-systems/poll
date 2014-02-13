<?php

/**
 * @file
 * Definition of Drupal\poll\EventSubscriber\PollSubscriber.
 */

namespace Drupal\poll\EventSubscriber;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class PollSubscriber
 *
 * Creates a poll choice field for the module.
 *
 * @package Drupal\poll\EventSubscriber
 */
class PollSubscriber implements EventSubscriberInterface {

  /**
   * Create a poll choice field if it doesn't exist.
   *
   * @param GetResponseEvent $event
   */
  public function onKernelRequestPollChoiceFieldCheck(GetResponseEvent $event) {
    $request = $event->getRequest();
    $poll_choice_field_exists = $request->attributes->get('_poll_choice_field_exists');
    if(!$poll_choice_field_exists) {
      poll_install_choice_field();
      $request->attributes->set('_poll_choice_field_exists', TRUE);
    }
  }

  /**
   * Registers the methods in this class that should be listeners.
   *
   * @return array
   *   An array of event listener definitions.
   */
  static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = array('onKernelRequestPollChoiceFieldCheck', 40);
    return $events;
  }

}
