<?php

/**
 * @file
 * Definition of Drupal\poll\PollRenderController.
 */

namespace Drupal\poll;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityRenderController;
use Drupal\entity\Entity\EntityDisplay;

/**
 * Render controller for poll terms.
 */
class PollRenderController extends EntityRenderController {

  /**
   * Overrides Drupal\Core\Entity\EntityRenderController::buildContent().
   */
  public function buildContent(array $entities, array $displays, $view_mode, $langcode = NULL) {
    parent::buildContent($entities, $displays, $view_mode, $langcode);

    foreach ($entities as $entity) {

      // Add the description if enabled.
//      $display = $displays[$entity->bundle()];
//      if (!empty($entity->description->value) && $display->getComponent('description')) {
//        $entity->content['description'] = array(
//          '#markup' => check_markup($entity->description->value, $entity->format->value, '', TRUE),
//          '#prefix' => '<div class="poll-term-description">',
//          '#suffix' => '</div>',
//        );
//      }

      $entity->content['description'] = 'TODO:';
    }

  }

  /**
   * Overrides \Drupal\Core\Entity\EntityRenderController::getBuildDefaults().
   */
  protected function getBuildDefaults(EntityInterface $entity, $view_mode, $langcode) {
    $return = parent::getBuildDefaults($entity, $view_mode, $langcode);

    return $return;
  }

}
