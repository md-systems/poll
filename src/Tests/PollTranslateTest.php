<?php

/**
 * @file
 * Definition of Drupal\poll\Tests\PollTranslateTest.
 */

namespace Drupal\poll\Tests;

use Drupal\Core\Language\Language;

/**
 * Tests poll translation logic.
 *
 * @group poll
 * @requires module test_skipped_not_ported
 */
class PollTranslateTest extends PollTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('language', 'content_translation');

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $language = new Language(array(
      'id' => 'zh-hans',
    ));
    language_save($language);
  }

  /**
   * Tests poll creation and translation.
   *
   * Checks that the choice names get copied from the original poll and that
   * the vote count values are set to 0.
   */
  function testPollTranslate() {
    $admin_user = $this->drupalCreateUser(array('administer content types', 'administer languages', 'administer polls', 'translate all content'));

    // Set up a poll with two choices.
    $title = $this->randomMachineName();
    $choices = array($this->randomMachineName(), $this->randomMachineName());
    $poll_nid = $this->pollCreate($title, $choices, FALSE);
    $this->assertTrue($poll_nid, 'Poll for translation logic test created.');

    $this->drupalLogout();
    $this->drupalLogin($admin_user);

    // Set "Poll" content type to use multilingual support with translation.
    $this->drupalGet('admin/structure/types/manage/poll');
    $edit = array('language_configuration[language_show]' => TRUE, 'node_type_language_translation_enabled' => TRUE);
    $this->drupalPost('admin/structure/types/manage/poll', $edit, t('Save content type'));
    $this->assertRaw(t('The content type %type has been updated.', array('%type' => 'Poll')), 'Poll content type has been updated.');

    // Edit poll.
    $this->drupalGet("node/$poll_nid/edit");
    $edit = array();
    // Set the poll's first choice count to 200.
    $edit['choice[chid:1][chvotes]'] = 200;
    // Set the language to Dutch.
    $edit['langcode'] = 'nl';
    $this->drupalPost(NULL, $edit, t('Save'));

    // Translate the Dutch poll.
    $this->drupalGet('node/add/poll', array('query' => array('translation' => $poll_nid, 'target' => 'en')));

    $dutch_poll = node_load($poll_nid);

    // Check that the vote count values didn't get copied from the Dutch poll
    // and are set to 0.
    $this->assertFieldByName('choice[chid:1][chvotes]', '0', ('Found choice with vote count 0'));
    $this->assertFieldByName('choice[chid:2][chvotes]', '0', ('Found choice with vote count 0'));
    // Check that the choice names got copied from the Dutch poll.
    $this->assertFieldByName('choice[chid:1][chtext]', $dutch_poll->choice[1]['chtext'], format_string('Found choice with text @text', array('@text' => $dutch_poll->choice[1]['chtext'])));
    $this->assertFieldByName('choice[chid:2][chtext]', $dutch_poll->choice[2]['chtext'], format_string('Found choice with text @text', array('@text' => $dutch_poll->choice[2]['chtext'])));
  }
}
