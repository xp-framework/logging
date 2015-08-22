<?php namespace util\log\unittest;

use util\log\layout\PatternLayout;
use util\log\context\MappedLogContext;
use util\log\LoggingEvent;
use util\log\LogLevel;
use util\log\LogCategory;

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

  #[@test, @expect('lang.IllegalArgumentException')]
  public function illegalFormatToken() {
    new PatternLayout('%Q');
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function unterminatedFormatToken() {
    new PatternLayout('%');
  }

  #[@test]
  public function literalPercent() {
    $this->assertEquals(
      '100%',
      (new PatternLayout('100%%'))->format($this->newLoggingEvent())
    );
  }

  #[@test]
  public function simpleFormat() {
    $this->assertEquals(
      'WARN [default] Hello',
      (new PatternLayout('%L [%c] %m'))->format($this->newLoggingEvent())
    );
  }

  #[@test]
  public function defaultFormat() {
    $this->assertEquals(
      '[16:08:04 1214 warn] Hello',
      (new PatternLayout('[%t %p %l] %m'))->format($this->newLoggingEvent())
    );
  }

  #[@test]
  public function tokenContext() {
    $context= new MappedLogContext();
    $context->put('key1', 'val1');

    $this->assertEquals(
      'key1=val1',
      (new PatternLayout('%x'))->format($this->newLoggingEvent(new LogCategory('default', LogLevel::ALL, $context)))
    );
  }
}
