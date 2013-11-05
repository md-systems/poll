<?php

/**
 * @file
 * Contains \Drupal\poll\Entity\PollInterface.
 */

namespace Drupal\poll;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining a block entity.
 */
interface PollInterface extends ConfigEntityInterface {

  /**
   * Returns the question being put to vote.
   *
   * @return string
   *   The question.
   */
  public function getQuestion();

  /**
   * Sets the poll question.
   *
   * @param string $question
   *   The new question.
   *
   */
  public function setQuestion($question);

  public function getRuntime();

  public function setRuntime($runtime);

  public function setAnonymousVoteAllow($anonymousVoteAllow);

  public function setCancelVoteAllow($cancelVoteAllow);

  public function setResultVoteAllow($resultVoteAllow);

  public function isAnonymousVoteAllow();

  public function isCancelVoteAllow();

  public function isResultVoteAllow();

  /**
   * Returns TRUE if the user is active.
   *
   * @return bool
   *   TRUE if the user is active, false otherwise.
   */
  public function isActive();

  /**
   * Activates the poll.
   *
   * @return \Drupal\poll\PollInterface
   *   The called poll entity.
   */
  public function activate();

  /**
   * Returns TRUE if the user is blocked.
   *
   * @return bool
   *   TRUE if the user is blocked, false otherwise.
   */
  public function isClosed();

  /**
   * Closes the poll.
   *
   * @return \Drupal\poll\PollInterface
   *   The called poll entity.
   */
  public function close();


}
