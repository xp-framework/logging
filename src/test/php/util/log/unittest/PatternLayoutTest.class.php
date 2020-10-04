<?php namespace util\log\unittest;

use lang\IllegalArgumentException;
use unittest\{Expect, Test};
use util\log\context\MappedLogContext;
use util\log\layout\PatternLayout;
use util\log\{LogCategory, LogLevel, LoggingEvent};

class PatternLayoutTest extends \unittest\TestCase {

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
    $this->assertEquals('Hello', $this->format('%m'));
  }

  #[Test]
  public function category_name() {
    $this->assertEquals('default', $this->format('%c'));
  }

  #[Test]
  public function lowercase_loglevel() {
    $this->assertEquals('warn', $this->format('%l'));
  }

  #[Test]
  public function uppercase_loglevel() {
    $this->assertEquals('WARN', $this->format('%L'));
  }

  #[Test]
  public function date_in_YYYY_MM_DD() {
    $this->assertEquals('2009-11-20', $this->format('%d'));
  }

  #[Test]
  public function time_in_HH_MM_SS() {
    $this->assertEquals('16:08:04', $this->format('%t'));
  }

  #[Test]
  public function process_id() {
    $this->assertEquals('1214', $this->format('%p'));
  }

  #[Test]
  public function literal_percent() {
    $this->assertEquals('100%', $this->format('100%%'));
  }

  #[Test]
  public function line_break() {
    $this->assertEquals("\n", $this->format('%n'));
  }

  #[Test]
  public function context_when_not_available() {
    $this->assertEquals('', $this->format('%x'));
  }

  #[Test]
  public function context() {
    $context= new MappedLogContext();
    $context->put('key1', 'val1');

    $this->assertEquals('key1=val1', $this->format('%x', new LogCategory('default', LogLevel::ALL, $context)));
  }

  #[Test]
  public function simple_format() {
    $this->assertEquals('WARN [default] Hello', $this->format('%L [%c] %m'));
  }

  #[Test]
  public function default_format() {
    $this->assertEquals('[16:08:04 1214 warn] Hello', $this->format('[%t %p %l] %m'));
  }
}