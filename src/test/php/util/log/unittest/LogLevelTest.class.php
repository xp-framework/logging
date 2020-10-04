<?php namespace util\log\unittest;

use lang\IllegalArgumentException;
use unittest\{Expect, Test};
use util\log\LogLevel;

class LogLevelTest extends \unittest\TestCase {

  #[Test]
  public function named_info() {
    $this->assertEquals(LogLevel::INFO, LogLevel::named('INFO'));
  }

  #[Test]
  public function named_warn() {
    $this->assertEquals(LogLevel::WARN, LogLevel::named('WARN'));
  }

  #[Test]
  public function named_error() {
    $this->assertEquals(LogLevel::ERROR, LogLevel::named('ERROR'));
  }

  #[Test]
  public function named_debug() {
    $this->assertEquals(LogLevel::DEBUG, LogLevel::named('DEBUG'));
  }

  #[Test]
  public function named_all() {
    $this->assertEquals(LogLevel::ALL, LogLevel::named('ALL'));
  }

  #[Test, Expect(IllegalArgumentException::class)]
  public function unknown() {
    LogLevel::named('@UNKNOWN@');
  }

  #[Test]
  public function nameOf_info() {
    $this->assertEquals('INFO', LogLevel::nameOf(LogLevel::INFO));
  }

  #[Test]
  public function nameOf_warn() {
    $this->assertEquals('WARN', LogLevel::nameOf(LogLevel::WARN));
  }

  #[Test]
  public function nameOf_error() {
    $this->assertEquals('ERROR', LogLevel::nameOf(LogLevel::ERROR));
  }

  #[Test]
  public function nameOf_debug() {
    $this->assertEquals('DEBUG', LogLevel::nameOf(LogLevel::DEBUG));
  }

  #[Test, Expect(IllegalArgumentException::class)]
  public function nameOf_illegal_loglevel() {
    LogLevel::nameOf(-1);
  }
}