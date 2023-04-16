<?php namespace util\log\unittest;

use test\Assert;
use test\Test;
use util\log\BufferedAppender;
use util\log\layout\PatternLayout;

class BufferedAppenderTest extends AppenderTest {

  /**
   * Creates new appender fixture
   *
   * @return  util.log.BufferedAppender
   */
  private function newFixture() {
    return (new BufferedAppender())->withLayout(new PatternLayout("[%l] %m\n"));
  }

  #[Test]
  public function buffer_initially_empty() {
    Assert::equals('', $this->newFixture()->getBuffer());
  }

  #[Test]
  public function append_one_message() {
    $fixture= $this->newFixture();
    $fixture->append($this->newEvent(\util\log\LogLevel::WARN, 'Test'));
    Assert::equals(
      "[warn] Test\n",
      $fixture->getBuffer()
    );
  }

  #[Test]
  public function append_two_messages() {
    $fixture= $this->newFixture();
    $fixture->append($this->newEvent(\util\log\LogLevel::WARN, 'Test'));
    $fixture->append($this->newEvent(\util\log\LogLevel::INFO, 'Just testing'));
    Assert::equals(
      "[warn] Test\n[info] Just testing\n",
      $fixture->getBuffer()
    );
  }

  #[Test]
  public function clear() {
    $fixture= $this->newFixture();
    $fixture->clear();
    Assert::equals('', $fixture->getBuffer());
  }

  #[Test]
  public function clear_after_appending() {
    $fixture= $this->newFixture();
    $fixture->append($this->newEvent(\util\log\LogLevel::WARN, 'Test'));
    $fixture->clear();
    Assert::equals('', $fixture->getBuffer());
  }
}