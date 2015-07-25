<?php

/**
 * @file
 * Definition of Drupal\poll\Tests\PollViewTest.
 */

namespace Drupal\poll\Tests;

/**
 * Tests the permissions checking of the poll admin view.
 *
 * @group poll
 */
class PollViewTest extends PollTestBase {
  /**
   * Tests viewing polls as admin and then authenticated user.
   */
  function testViewPermissions() {
    $this->drupalGet('admin/content/poll/');
    $this->assertResponse(200);
    $test_user = $this->createUser(['access administration pages']);
    $this->drupalLogin($test_user);
    $this->drupalGet('admin/content/poll/');
    $this->assertResponse(403);
  }
}
