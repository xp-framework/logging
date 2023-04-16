<?php namespace util\log\unittest;

use lang\IllegalArgumentException;
use test\Assert;
use test\{Expect, Test};
use util\log\LogLevel;

class LogLevelTest {

  #[Test]
  public function named_info() {
    Assert::equals(LogLevel::INFO, LogLevel::named('INFO'));
  }

  #[Test]
  public function named_warn() {
    Assert::equals(LogLevel::WARN, LogLevel::named('WARN'));
  }

  #[Test]
  public function named_error() {
    Assert::equals(LogLevel::ERROR, LogLevel::named('ERROR'));
  }

  #[Test]
  public function named_debug() {
    Assert::equals(LogLevel::DEBUG, LogLevel::named('DEBUG'));
  }

  #[Test]
  public function named_all() {
    Assert::equals(LogLevel::ALL, LogLevel::named('ALL'));
  }

  #[Test, Expect(IllegalArgumentException::class)]
  public function unknown() {
    LogLevel::named('@UNKNOWN@');
  }

  #[Test]
  public function nameOf_info() {
    Assert::equals('INFO', LogLevel::nameOf(LogLevel::INFO));
  }

  #[Test]
  public function nameOf_warn() {
    Assert::equals('WARN', LogLevel::nameOf(LogLevel::WARN));
  }

  #[Test]
  public function nameOf_error() {
    Assert::equals('ERROR', LogLevel::nameOf(LogLevel::ERROR));
  }

  #[Test]
  public function nameOf_debug() {
    Assert::equals('DEBUG', LogLevel::nameOf(LogLevel::DEBUG));
  }

  #[Test, Expect(IllegalArgumentException::class)]
  public function nameOf_illegal_loglevel() {
    LogLevel::nameOf(-1);
  }
}