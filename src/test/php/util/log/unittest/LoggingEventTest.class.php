<?php namespace util\log\unittest;

use test\{Assert, Before, Test};
use util\log\{LogCategory, LogLevel, LoggingEvent};

class LoggingEventTest {
  private $cat, $fixture;

  #[Before]
  public function fixture() {
    $this->cat= new LogCategory('default', null, null, 0);
    $this->fixture= new LoggingEvent($this->cat, 1258733284, 1, LogLevel::INFO, ['Hello']);
  }

  #[Test]
  public function getCategory() {
    Assert::equals($this->cat, $this->fixture->getCategory());
  }
 
  #[Test]
  public function getTimestamp() {
    Assert::equals(1258733284, $this->fixture->getTimestamp());
  }

  #[Test]
  public function getProcessId() {
    Assert::equals(1, $this->fixture->getProcessId());
  }

  #[Test]
  public function getLevel() {
    Assert::equals(LogLevel::INFO, $this->fixture->getLevel());
  }

  #[Test]
  public function getArguments() {
    Assert::equals(['Hello'], $this->fixture->getArguments());
  }
}