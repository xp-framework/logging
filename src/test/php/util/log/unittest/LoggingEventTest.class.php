<?php namespace util\log\unittest;

use util\log\LoggingEvent;
use util\log\LogCategory;
use util\log\LogLevel;

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

  #[@test]
  public function getCategory() {
    $this->assertEquals(new LogCategory('default', null, null, 0), $this->fixture->getCategory());
  }
 
  #[@test]
  public function getTimestamp() {
    $this->assertEquals(1258733284, $this->fixture->getTimestamp());
  }

  #[@test]
  public function getProcessId() {
    $this->assertEquals(1, $this->fixture->getProcessId());
  }

  #[@test]
  public function getLevel() {
    $this->assertEquals(LogLevel::INFO, $this->fixture->getLevel());
  }

  #[@test]
  public function getArguments() {
    $this->assertEquals(['Hello'], $this->fixture->getArguments());
  }
}
