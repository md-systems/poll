<?php

/**
 * @file
 * Definition of Drupal\poll\Tests\PollBlockTest.
 */

namespace Drupal\poll\Tests;

use Drupal\Component\Utility\String;

/**
 * Tests the recent poll block.
 */
class PollBlockTest extends PollTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('block');

  public static function getInfo() {
    return array(
      'name' => 'Block availability',
      'description' => 'Check if the most recent poll block is available.',
      'group' => 'Poll',
    );
  }

  function setUp() {
    parent::setUp();

    // Enable the recent poll block.
    $label = $this->poll->label();
    $this->drupalPlaceBlock('poll_recent_block', array('label' => $label));
  }

  /**
   * Tests creating, viewing, voting on recent poll block.
   */
  function testRecentBlock() {
    // Enable the recent poll block.
    $this->drupalPlaceBlock('poll_recent_block');

    // Verify poll appears in a block.
    $this->drupalLogin($this->web_user);
    $this->drupalGet('user');

    // If a 'block' view not generated, this title would not appear even though
    // the choices might.
    $this->assertText($this->poll->label(), String::format('@title Poll appears in block.', array('@title' => $this->poll->label())));

    // Logout and login back in as a user who can vote.
    $this->drupalLogout();
    $vote_user = $this->drupalCreateUser(array('access polls', 'administer polls'));
    $this->drupalLogin($vote_user);

    // Verify we can vote via the block.
    $edit = array(
      'choice' => '1',
    );

    $this->drupalPostForm('user/' . $vote_user->id(), $edit, t('Vote'));
    $this->assertText('Your vote has been recorded.', 'Your vote has been recorded.');
    $this->assertText('Total votes:  1', 'Vote count updated correctly.');

    $this->assertText('Older polls', 'Link to older polls appears.');
    $this->clickLink('Older polls');
    $this->assertText('1 vote - open', 'Link to poll listing correct.');


    // Close the poll and verify block doesn't appear.
    $content_user = $this->drupalCreateUser(array('access polls', 'administer polls'));
    $this->drupalLogout();
    $this->drupalLogin($content_user);
    $this->poll->close();
    $this->poll->save();
    $this->drupalGet('user/' . $content_user->id());
    $this->assertNoText($this->poll->label(), 'Poll no longer appears in block.');
  }
}
