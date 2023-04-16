<?php namespace util\log\unittest;

use lang\Value;
use test\{Assert, Before, Test};
use util\log\layout\DefaultLayout;
use util\log\{LogCategory, LogLevel, LoggingEvent};

class DefaultLayoutTest {
  private $fixture, $tz;

  #[Before]
  public function setUp() {
    $this->fixture= new DefaultLayout();

    // Save timezone, it will be restored inside tearDown()
    $this->tz= date_default_timezone_get();
    date_default_timezone_set('Europe/Berlin');
  }

  #[After]
  public function tearDown() {
    date_default_timezone_set($this->tz);
  }

  /**
   * Creates new logging event
   *
   * @param   int level see util.log.LogLevel
   * @param   string message
   * @return  util.log.LoggingEvent
   */
  private function newEvent($level, $args) {
    return new LoggingEvent(new LogCategory('test'), 0, 0, $level, $args);
  }

  #[Test]
  public function debug() {
    Assert::equals(
      "[01:00:00     0 debug] Test\n",
      $this->fixture->format($this->newEvent(LogLevel::DEBUG, ['Test']))
    );
  }

  #[Test]
  public function info() {
    Assert::equals(
      "[01:00:00     0  info] Test\n",
      $this->fixture->format($this->newEvent(LogLevel::INFO, ['Test']))
    );
  }

  #[Test]
  public function warn() {
    Assert::equals(
      "[01:00:00     0  warn] Test\n",
      $this->fixture->format($this->newEvent(LogLevel::WARN, ['Test']))
    );
  }

  #[Test]
  public function error() {
    Assert::equals(
      "[01:00:00     0 error] Test\n",
      $this->fixture->format($this->newEvent(LogLevel::ERROR, ['Test']))
    );
  }
}