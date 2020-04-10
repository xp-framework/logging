<?php namespace util\log\unittest;

use lang\IllegalArgumentException;
use util\log\LogLevel;

class LogLevelTest extends \unittest\TestCase {

  #[@test]
  public function named_info() {
    $this->assertEquals(LogLevel::INFO, LogLevel::named('INFO'));
  }

  #[@test]
  public function named_warn() {
    $this->assertEquals(LogLevel::WARN, LogLevel::named('WARN'));
  }

  #[@test]
  public function named_error() {
    $this->assertEquals(LogLevel::ERROR, LogLevel::named('ERROR'));
  }

  #[@test]
  public function named_debug() {
    $this->assertEquals(LogLevel::DEBUG, LogLevel::named('DEBUG'));
  }

  #[@test]
  public function named_all() {
    $this->assertEquals(LogLevel::ALL, LogLevel::named('ALL'));
  }

  #[@test, @expect(IllegalArgumentException::class)]
  public function unknown() {
    LogLevel::named('@UNKNOWN@');
  }

  #[@test]
  public function nameOf_info() {
    $this->assertEquals('INFO', LogLevel::nameOf(LogLevel::INFO));
  }

  #[@test]
  public function nameOf_warn() {
    $this->assertEquals('WARN', LogLevel::nameOf(LogLevel::WARN));
  }

  #[@test]
  public function nameOf_error() {
    $this->assertEquals('ERROR', LogLevel::nameOf(LogLevel::ERROR));
  }

  #[@test]
  public function nameOf_debug() {
    $this->assertEquals('DEBUG', LogLevel::nameOf(LogLevel::DEBUG));
  }

  #[@test, @expect(IllegalArgumentException::class)]
  public function nameOf_illegal_loglevel() {
    LogLevel::nameOf(-1);
  }
}