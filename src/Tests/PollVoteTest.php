<?php

/**
 * @file
 * Definition of Drupal\poll\Tests\PollVoteTest.
 */

namespace Drupal\poll\Tests;

/**
 * Tests voting on a poll.
 *
 * @group poll
 */
class PollVoteTest extends PollTestBase {

  /**
   * Tests voting on a poll.
   */
  function testPollVote() {

    $this->drupalLogin($this->web_user);

    // Record a vote for the first choice.
    $edit = array(
      'choice' => '1',
    );
    $this->drupalPostForm('poll/' . $this->poll->id(), $edit, t('Vote'));
    $this->assertText('Your vote has been recorded.', 'Your vote was recorded.');
    $this->assertText('Total votes:  1', 'Vote count updated correctly.');
    $elements = $this->xpath('//input[@value="Cancel vote"]');
    $this->assertTrue(isset($elements[0]), "'Cancel your vote' button appears.");

//    $this->drupalGet('poll/' . $this->poll->id() . '/votes');
//    $this->assertText(t('This table lists all the recorded votes for this poll. If anonymous users are allowed to vote, they will be identified by the IP address of the computer they used when they voted.'), 'Vote table text.');
//    $options = $this->poll->getOptions();
//    debug($options);

   // $this->assertText($this->poll->getOptions()[0], 'Vote recorded');

    // Ensure poll listing page has correct number of votes.
//    $this->drupalGet('poll');
//    $this->assertText($this->poll->label(), 'Poll appears in poll list.');
//    $this->assertText('1 vote', 'Poll has 1 vote.');

    // Cancel a vote.
    $this->drupalPostForm('poll/' . $this->poll->id(), array(), t('Cancel vote'));
    $this->assertText("Are you sure you want to delete this vote for " . $this->poll->label(), 'Vote delete confirm form found.');

    $this->drupalPostForm(NULL, array(), t('Delete'));
    $this->assertText('Your vote was cancelled.', 'Your vote was cancelled.');
    $this->assertNoText('Cancel your vote', "Cancel vote button doesn't appear.");

//    $this->drupalGet('poll/' . $this->poll->id() . '/votes');
//    $this->assertNoText($choices[0], 'Vote cancelled');

    // Ensure poll listing page has correct number of votes.
//    $this->drupalGet('poll');
//    $this->assertText($title, 'Poll appears in poll list.');
//    $this->assertText('0 votes', 'Poll has 0 votes.');

    // Log in as a user who can only vote on polls.
//    $this->drupalLogout();
//    $this->drupalLogin($restricted_vote_user);

    // Vote on a poll.
    $edit = array(
      'choice' => '1',
    );
    $this->drupalPostForm('poll/' . $this->poll->id(), $edit, t('Vote'));
    $this->assertText('Your vote has been recorded.', 'Your vote was recorded.');
    $this->assertText('Total votes:  1', 'Vote count updated correctly.');
    $elements = $this->xpath('//input[@value="Cancel your vote"]');
    $this->assertTrue(empty($elements), "'Cancel your vote' button does not appear.");

    $this->drupalLogin($this->admin_user);

    $this->drupalGet('admin/content/poll');
    $this->assertText($this->poll->label());

    // Test for the overview page.
    $field_status = $this->xpath('//table/tbody/tr[1]');
    $active = (string) $field_status[0]->td[1];
    $this->assertEqual(trim($active), 'Yes');

    $anonymous_votes = trim((string) $field_status[0]->td[2]);
    $this->assertEqual($anonymous_votes, 'Off');

    // Edit the poll.
    $this->clickLink($this->poll->label());
    $this->clickLink('Edit');

    // Add the runtime date and allow anonymous to vote.
    $edit = array(
      'runtime' => 172800,
      'anonymous_vote_allow[value]' => TRUE,
    );

    $this->drupalPostForm(NULL, $edit, t('Save'));

    // Assert that editing was successful.
    $this->assertText('The poll ' . $this->poll->label() . ' has been updated.');

    // Check if the active label is correct.
    $field_status = $this->xpath('//table/tbody/tr[1]');
    $active = trim((string) $field_status[0]->td[1]);
    $date = \Drupal::service('date.formatter')->format($this->poll->getCreated() + 172800, 'short');
    $output = 'Yes (until ' . rtrim(strstr($date, '-', TRUE)) . ')';
    $this->assertEqual($active, $output);

    // Check if allow anonymous voting is on.
    $anonymous_votes = trim((string) $field_status[0]->td[2]);
    $this->assertEqual($anonymous_votes, 'On');

    // Check the number of total votes.
    $total_votes = trim((string) $field_status[0]->td[4]);
    $this->assertEqual($total_votes, '1');
  }

  /**
   * Tests voting on a poll using AJAX.
   */
  public function testAjaxPollVote() {

    $this->drupalLogin($this->web_user);

    // Record a vote for the first choice.
    $edit = array(
      'choice' => '1',
    );
    $this->drupalPostAjaxForm('poll/' . $this->poll->id(), $edit, array('op' => 'Vote'), NULL, array(), array(), 'poll-view-form-1');
    $this->assertText('Your vote has been recorded.', 'Your vote was recorded.');
    $this->assertText('Total votes:  1', 'Vote count updated correctly.');
    $elements = $this->xpath('//input[@value="Cancel vote"]');
    $this->assertTrue(isset($elements[0]), "'Cancel your vote' button appears.");
  }

}
