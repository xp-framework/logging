<?php namespace util\log\unittest;

use lang\Value;
use unittest\{Test, TestCase};
use util\log\layout\DefaultLayout;
use util\log\{LogCategory, LogLevel, LoggingEvent};

class DefaultLayoutTest extends \unittest\TestCase {
  private $fixture, $tz;

  /** @return void */
  public function setUp() {
    $this->fixture= new DefaultLayout();
    $this->tz= date_default_timezone_get();
    date_default_timezone_set('Europe/Berlin');
  }

  /** @return void */
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
  public function newEvent($level, $args) {
    return new LoggingEvent(new LogCategory('test'), 0, 0, $level, $args);
  }

  #[Test]
  public function debug() {
    $this->assertEquals(
      "[01:00:00     0 debug] Test\n",
      $this->fixture->format($this->newEvent(LogLevel::DEBUG, ['Test']))
    );
  }

  #[Test]
  public function info() {
    $this->assertEquals(
      "[01:00:00     0  info] Test\n",
      $this->fixture->format($this->newEvent(LogLevel::INFO, ['Test']))
    );
  }

  #[Test]
  public function warn() {
    $this->assertEquals(
      "[01:00:00     0  warn] Test\n",
      $this->fixture->format($this->newEvent(LogLevel::WARN, ['Test']))
    );
  }

  #[Test]
  public function error() {
    $this->assertEquals(
      "[01:00:00     0 error] Test\n",
      $this->fixture->format($this->newEvent(LogLevel::ERROR, ['Test']))
    );
  }
}