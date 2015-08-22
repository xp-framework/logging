<?php namespace util\log\unittest;

use util\log\layout\PatternLayout;
use util\log\context\MappedLogContext;
use util\log\LoggingEvent;
use util\log\LogLevel;
use util\log\LogCategory;
use lang\IllegalArgumentException;

class PatternLayoutTest extends \unittest\TestCase {

  /**
   * Creates a new logging event
   *
   * @param  util.log.LogCategory $cat
   * @return util.log.LoggingEvent
   */
  private function newLoggingEvent($cat= null) {
    return new LoggingEvent(
      $cat ?: new LogCategory('default'), 
      1258733284, 
      1214, 
      LogLevel::WARN, 
      ['Hello']
    );   
  }

  #[@test, @expect(IllegalArgumentException::class)]
  public function illegal_format_token() {
    new PatternLayout('%Q');
  }

  #[@test, @expect(IllegalArgumentException::class)]
  public function unterminated_format_token() {
    new PatternLayout('%');
  }

  #[@test]
  public function message() {
    $this->assertEquals('Hello', (new PatternLayout('%m'))->format($this->newLoggingEvent()));
  }

  #[@test]
  public function category_name() {
    $this->assertEquals('default', (new PatternLayout('%c'))->format($this->newLoggingEvent()));
  }

  #[@test]
  public function lowercase_loglevel() {
    $this->assertEquals('warn', (new PatternLayout('%l'))->format($this->newLoggingEvent()));
  }

  #[@test]
  public function uppercase_loglevel() {
    $this->assertEquals('WARN', (new PatternLayout('%L'))->format($this->newLoggingEvent()));
  }

  #[@test]
  public function date_in_YYYY_MM_DD() {
    $this->assertEquals('2009-11-20', (new PatternLayout('%d'))->format($this->newLoggingEvent()));
  }

  #[@test]
  public function time_in_HH_MM_SS() {
    $this->assertEquals('16:08:04', (new PatternLayout('%t'))->format($this->newLoggingEvent()));
  }

  #[@test]
  public function process_id() {
    $this->assertEquals('1214', (new PatternLayout('%p'))->format($this->newLoggingEvent()));
  }

  #[@test]
  public function literal_percent() {
    $this->assertEquals('100%', (new PatternLayout('100%%'))->format($this->newLoggingEvent()));
  }

  #[@test]
  public function line_break() {
    $this->assertEquals("\n", (new PatternLayout('%n'))->format($this->newLoggingEvent()));
  }

  #[@test]
  public function context_when_not_available() {
    $this->assertEquals('', (new PatternLayout('%x'))->format($this->newLoggingEvent()));
  }

  #[@test]
  public function context() {
    $context= new MappedLogContext();
    $context->put('key1', 'val1');

    $this->assertEquals(
      'key1=val1',
      (new PatternLayout('%x'))->format($this->newLoggingEvent(new LogCategory('default', LogLevel::ALL, $context)))
    );
  }

  #[@test]
  public function simple_format() {
    $this->assertEquals(
      'WARN [default] Hello',
      (new PatternLayout('%L [%c] %m'))->format($this->newLoggingEvent())
    );
  }

  #[@test]
  public function default_format() {
    $this->assertEquals(
      '[16:08:04 1214 warn] Hello',
      (new PatternLayout('[%t %p %l] %m'))->format($this->newLoggingEvent())
    );
  }

}