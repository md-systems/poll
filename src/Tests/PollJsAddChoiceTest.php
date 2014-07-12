<?php

/**
 * @file
 * Definition of Drupal\poll\Tests\PollJsAddChoiceTest.
 */

namespace Drupal\poll\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Tests adding new choices to a poll.
 */
class PollJsAddChoiceTest extends WebTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('poll');

  public static function getInfo() {
    return array(
      'name' => 'Poll add choice',
      'description' => 'Submits a POST request for an additional poll choice.',
      'group' => 'Poll'
    );
  }

  /**
   * Tests adding a new choice to a poll.
   */
  function testAddChoice() {
    $admin_user = $this->drupalCreateUser(array('administer polls'));
    $this->drupalLogin($admin_user);

    foreach (array('js', 'nojs') as $js) {
      $this->drupalGet('poll/add');
      $field0 = $this->randomName();
      $field1 = $this->randomName();
      $field2 = $this->randomName();
      $edit = array();
      $edit['question[0][value]'] = $this->randomName();
      $edit['field_choice[0][choice]'] = $field0;

      if ($js == 'js') {
        $this->drupalPostAjaxForm(NULL, $edit, 'field_choice_add_more');
        $this->drupalPostAjaxForm(NULL, $edit, 'field_choice_add_more');
      }
      else {
        $this->drupalPostForm(NULL, $edit, t('Add another item'));
        $this->drupalPostForm(NULL, $edit, t('Add another item'));
      }

      $edit['field_choice[1][choice]'] = $field1;
      $edit['field_choice[2][choice]'] = $field2;
      $this->drupalPostForm(NULL, $edit, t('Save'));

      $polls = entity_load_multiple_by_properties('poll', array('question' => $edit['question[0][value]']));
      $new_poll = reset($polls);

      $this->assertEqual($new_poll->field_choice[0]->choice, $field0, format_string('@js : The field value was correctly saved.', array('@js' => $js)));
      $this->assertEqual($new_poll->field_choice[1]->choice, $field1, format_string('@js : The field value was correctly saved.', array('@js' => $js)));
      $this->assertEqual($new_poll->field_choice[2]->choice, $field2, format_string('@js : The field value was correctly saved.', array('@js' => $js)));

    }
  }
}
