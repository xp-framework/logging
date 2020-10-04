<?php namespace util\log\unittest;

use unittest\Test;
use util\log\{LogCategory, LogLevel, LoggingEvent};

class LoggingEventTest extends \unittest\TestCase {
  private $fixture;

  /** @return void */
  public function setUp() {
    $this->fixture= new LoggingEvent(
      new LogCategory('default', null, null, 0), 
      1258733284, 
      1, 
      LogLevel::INFO, 
      ['Hello']
    );
  }

  #[Test]
  public function getCategory() {
    $this->assertEquals(new LogCategory('default', null, null, 0), $this->fixture->getCategory());
  }
 
  #[Test]
  public function getTimestamp() {
    $this->assertEquals(1258733284, $this->fixture->getTimestamp());
  }

  #[Test]
  public function getProcessId() {
    $this->assertEquals(1, $this->fixture->getProcessId());
  }

  #[Test]
  public function getLevel() {
    $this->assertEquals(LogLevel::INFO, $this->fixture->getLevel());
  }

  #[Test]
  public function getArguments() {
    $this->assertEquals(['Hello'], $this->fixture->getArguments());
  }
}