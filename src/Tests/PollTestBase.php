<?php

/**
 * @file
 * Definition of Drupal\poll\Tests\PollTestBase.
 */

namespace Drupal\poll\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Defines a base class for testing the Poll module.
 */
abstract class PollTestBase extends WebTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('node', 'poll');

  /**
   * Creates a poll.
   *
   * @param string $title
   *   The title of the poll.
   * @param array $choices
   *   A list of choice labels.
   * @param boolean $preview
   *   (optional) Whether to test if the preview is working or not. Defaults to
   *   TRUE.
   *
   * @return
   *   The node id of the created poll, or FALSE on error.
   */
  function pollCreate($title, $choices, $preview = TRUE) {

    $this->assertTrue(TRUE, 'Create a poll');

    $admin_user = $this->drupalCreateUser(array('access polls', 'administer polls'));
    $web_user = $this->drupalCreateUser(array('access polls', 'access content', 'administer polls'));
    $this->drupalLogin($admin_user);

    // Get the form first to initialize the state of the internal browser.
    $this->drupalGet('poll/add');

    // Prepare a form with two choices.
    list($edit, $index) = $this->_pollGenerateEdit($title, $choices);

    // Verify that the vote count element only allows non-negative integers.
    /*
    $this->drupalPostForm(NULL, NULL, t('Add another item'));
    $this->drupalPostForm(NULL, NULL, t('Add another item'));
    $edit['field_choice[2][choice]'] = -1;
    $edit['field_choice[3][choice]'] = $this->randomString(7);
    $this->drupalPostForm(NULL, $edit, t('Save'));
    $this->assertText(t('Bitte wählen Sie einen Wert, der grösser ist als 0.'));
    $this->assertText(t('Bitte geben Sie eine Nummer ein.'));
    */

    // Repeat steps for initializing the state of the internal browser.
    $this->drupalLogin($web_user);
    $this->drupalGet('poll/add');
    list($edit, $index) = $this->_pollGenerateEdit($title, $choices);

    // Re-submit the form until all choices are filled in.
    if (count($choices) > 2) {
      while ($index < count($choices)) {
        $this->drupalPostForm(NULL, $edit, t('Add another item'));
        $this->assertPollChoiceOrder($choices, $index);
        list($edit, $index) = $this->_pollGenerateEdit($title, $choices, $index);
      }
    }
    debug($edit);

    /* No previewbutton to check with sascha.
    if ($preview) {
      $this->drupalPostForm(NULL, $edit, t('Preview'));
      $this->assertPollChoiceOrder($choices, $index, TRUE);
      list($edit, $index) = $this->_pollGenerateEdit($title, $choices, $index);
    }
    */

    $this->drupalPostForm(NULL, $edit, t('Save'));
    $poll = $this->drupalGetPollByQuestion($title);

    $this->assertText(t('The poll @title has been added.', array('@title' => $title)), 'The poll ' . $title . ' has been added.');
    $this->assertTrue($poll->id(), 'Poll has been found in the database.');

    $poll_id = $poll->id();

    return isset($poll_id) ? $poll_id : FALSE;
  }

  /**
   * Get a node from the database based on its title.
   *
   * @param $title
   *   A node title, usually generated by $this->randomName().
   * @param $reset
   *   (optional) Whether to reset the entity cache.
   *
   * @return \Drupal\node\NodeInterface
   *   A node entity matching $title.
   */
  function drupalGetPollByQuestion($question, $reset = FALSE) {
    if ($reset) {
      \Drupal::entityManager()->getStorage('poll')->resetCache();
    }
    $polls = entity_load_multiple_by_properties('poll', array('question' => $question));
    // Load the first poll returned from the database.
    $returned_poll = reset($polls);
    return $returned_poll;
  }

  /**
   * Generates POST values for the poll node form, specifically poll choices.
   *
   * @param $title
   *   The title for the poll node.
   * @param $choices
   *   An array containing poll choices, as generated by
   *   PollTestBase::_generateChoices().
   * @param $index
   *   (optional) The amount/number of already submitted poll choices. Defaults
   *   to 0.
   *
   * @return
   *   An indexed array containing:
   *   - The generated POST values, suitable for
   *     Drupal\simpletest\WebTestBase::drupalPostFrom().
   *   - The number of poll choices contained in 'edit', for potential re-usage
   *     in subsequent invocations of this function.
   */
  function _pollGenerateEdit($question, array $choices, $index = 0) {
    $max_new_choices = ($index == 0 ? 2 : 1);
    $already_submitted_choices = array_slice($choices, 0, $index);
    $new_choices = array_values(array_slice($choices, $index, $max_new_choices));

    $edit = array(
      'question[0][value]' => $question,
    );
    $this->drupalPostForm(NULL, NULL, t('Add another item'));
    foreach ($already_submitted_choices as $k => $text) {
      $edit['field_choice[' . $k  . '][choice]'] = $text;
    }
    foreach ($new_choices as $k => $text) {
      $edit['field_choice[' . $k . '][choice]'] = $text;
    }
    return array($edit, count($already_submitted_choices) + count($new_choices));
  }

  /*
   * Generates random choices for the poll.
   */
  function _generateChoices($count = 7) {
    $choices = array();
    for ($i = 1; $i <= $count; $i++) {
      $choices[] = $this->randomName();
    }
    return $choices;
  }

  /**
   * Asserts correct poll choice order in the node form after submission.
   *
   * Verifies both the order in the DOM and in the 'weight' form elements.
   *
   * @param $choices
   *   An array containing poll choices, as generated by
   *   PollTestBase::_generateChoices().
   * @param $index
   *   (optional) The amount/number of already submitted poll choices. Defaults
   *   to 0.
   * @param $preview
   *   (optional) Whether to also check the poll preview.
   *
   * @see PollTestBase::_pollGenerateEdit()
   */
  function assertPollChoiceOrder(array $choices, $index = 0, $preview = FALSE) {
    $expected = array();
    $weight = 0;
    foreach ($choices as $id => $label) {
      if ($id < $index) {
        // Directly assert the weight form element value for this choice.
        $this->assertFieldByName('field_choice[' . $id . '][_weight]', $weight, format_string('Found field_choice @id with weight @weight.', array(
          '@id' => $id,
          '@weight' => $weight,
        )));
        // The expected weight of each choice is higher than the previous one.
        $weight++;
        // Append to our (to be reversed) stack of labels.
        $expected[$weight] = $label;
      }
    }
    ksort($expected);

    // Verify DOM order of poll choices (i.e., #weight of form elements).
    $elements = $this->xpath('//input[starts-with(@name, :prefix) and contains(@name, :suffix)]', array(
      ':prefix' => 'choice[chid:',
      ':suffix' => '][chtext]',
    ));
    $expected_order = $expected;
    foreach ($elements as $element) {
      $next_label = array_shift($expected_order);
      $this->assertEqual((string) $element['value'], $next_label);
    }

    // If requested, also verify DOM order in preview.
    if ($preview) {
      $elements = $this->xpath('//div[contains(@class, :teaser)]/descendant::div[@class=:text]', array(
        ':teaser' => 'node-teaser',
        ':text' => 'text',
      ));
      $expected_order = $expected;
      foreach ($elements as $element) {
        $next_label = array_shift($expected_order);
        $this->assertEqual((string) $element, $next_label, format_string('Found choice @label in preview.', array(
          '@label' => $next_label,
        )));
      }
    }
  }
  /**
   * Tests updating a poll.
   */
  function pollUpdate($nid, $title, $edit) {
    // Edit the poll node.
    $this->drupalPostForm('node/' . $nid . '/edit', $edit, t('Save'));
    $this->assertText(t('@type @title has been updated.', array('@type' => node_type_get_names('poll'), '@title' => $title)), 'Poll has been updated.');
  }
}
