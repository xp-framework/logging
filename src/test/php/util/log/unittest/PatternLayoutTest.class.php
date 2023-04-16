<?php namespace util\log\unittest;

use lang\IllegalArgumentException;
use test\Assert;
use test\{Expect, Test};
use util\log\context\MappedLogContext;
use util\log\layout\PatternLayout;
use util\log\{LogCategory, LogLevel, LoggingEvent};

class PatternLayoutTest {

  /**
   * Formats a given logging event
   *
   * @param  string $pattern
   * @param  util.log.LogCategory $cat
   * @return string
   */
  private function format($pattern, $cat= null) {
    return (new PatternLayout($pattern))->format(new LoggingEvent(
      $cat ?: new LogCategory('default'), 
      1258733284, 
      1214, 
      LogLevel::WARN, 
      ['Hello']
    ));
  }

  #[Test, Expect(IllegalArgumentException::class)]
  public function illegal_format_token() {
    new PatternLayout('%Q');
  }

  #[Test, Expect(IllegalArgumentException::class)]
  public function unterminated_format_token() {
    new PatternLayout('%');
  }

  #[Test]
  public function message() {
    Assert::equals('Hello', $this->format('%m'));
  }

  #[Test]
  public function category_name() {
    Assert::equals('default', $this->format('%c'));
  }

  #[Test]
  public function lowercase_loglevel() {
    Assert::equals('warn', $this->format('%l'));
  }

  #[Test]
  public function uppercase_loglevel() {
    Assert::equals('WARN', $this->format('%L'));
  }

  #[Test]
  public function date_in_YYYY_MM_DD() {
    Assert::equals('2009-11-20', $this->format('%d'));
  }

  #[Test]
  public function time_in_HH_MM_SS() {
    Assert::equals('16:08:04', $this->format('%t'));
  }

  #[Test]
  public function process_id() {
    Assert::equals('1214', $this->format('%p'));
  }

  #[Test]
  public function literal_percent() {
    Assert::equals('100%', $this->format('100%%'));
  }

  #[Test]
  public function line_break() {
    Assert::equals("\n", $this->format('%n'));
  }

  #[Test]
  public function context_when_not_available() {
    Assert::equals('', $this->format('%x'));
  }

  #[Test]
  public function context() {
    $context= new MappedLogContext();
    $context->put('key1', 'val1');

    Assert::equals('key1=val1', $this->format('%x', new LogCategory('default', LogLevel::ALL, $context)));
  }

  #[Test]
  public function simple_format() {
    Assert::equals('WARN [default] Hello', $this->format('%L [%c] %m'));
  }

  #[Test]
  public function default_format() {
    Assert::equals('[16:08:04 1214 warn] Hello', $this->format('[%t %p %l] %m'));
  }
}