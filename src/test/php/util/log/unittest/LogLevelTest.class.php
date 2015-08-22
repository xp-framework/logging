<?php namespace util\log\unittest;

use unittest\TestCase;
use util\log\LogLevel;

class LogLevelTest extends TestCase {

  #[@test]
  public function namedInfo() {
    $this->assertEquals(LogLevel::INFO, LogLevel::named('INFO'));
  }

  #[@test]
  public function namedWarn() {
    $this->assertEquals(LogLevel::WARN, LogLevel::named('WARN'));
  }

  #[@test]
  public function namedError() {
    $this->assertEquals(LogLevel::ERROR, LogLevel::named('ERROR'));
  }

  #[@test]
  public function namedDebug() {
    $this->assertEquals(LogLevel::DEBUG, LogLevel::named('DEBUG'));
  }

  #[@test]
  public function namedAll() {
    $this->assertEquals(LogLevel::ALL, LogLevel::named('ALL'));
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function unknown() {
    LogLevel::named('@UNKNOWN@');
  }
}
