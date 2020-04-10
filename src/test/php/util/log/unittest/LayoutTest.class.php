<?php namespace util\log\unittest;

use lang\Value;
use unittest\TestCase;
use util\log\{Layout, LogCategory, LogLevel, LoggingEvent};

class LayoutTest extends TestCase {
  private $fixture, $tz;

  /** @return void */
  public function setUp() {
    $this->fixture= new class() extends Layout {
      public function format(LoggingEvent $event) {
        $s= '[log]';
        foreach ($event->getArguments() as $arg) {
          $s.= ' '.$this->stringOf($arg);
        }
        return $s;
      }
    };
    $this->tz= date_default_timezone_get();
    date_default_timezone_set('Europe/Berlin');
  }

  /** @return void */
  public function tearDown() {
    date_default_timezone_set($this->tz);
  }

  /**
   * Creates new logging event
   *
   * @param   int level see util.log.LogLevel
   * @param   string message
   * @return  util.log.LoggingEvent
   */
  public function newEvent($level, $args) {
    return new LoggingEvent(new LogCategory('test'), 0, 0, $level, $args);
  }

  #[@test, @values([
  #  [null, 'null'],
  #  [true, 'true'],
  #  [false, 'false'],
  #  [1, '1'],
  #  [1.5, '1.5'],
  #  ['Test', 'Test'],
  #])]
  public function formatting_scalars($value, $expected) {
    $this->assertEquals(
      '[log] '.$expected,
      $this->fixture->format($this->newEvent(LogLevel::DEBUG, [$value]))
    );
  }

  #[@test, @values([
  #  [[], '[]'],
  #  [[1], '[1]'],
  #  [[1, 2, 3], '[1, 2, 3]'],
  #  [[[1]], '[[1]]'],
  #])]
  public function formatting_arrays($value, $expected) {
    $this->assertEquals(
      '[log] '.$expected,
      $this->fixture->format($this->newEvent(LogLevel::DEBUG, [$value]))
    );
  }

  #[@test]
  public function formatting_map() {
    $this->assertEquals(
      "[log] [\n  key => \"value\"\n]",
      $this->fixture->format($this->newEvent(LogLevel::DEBUG, [['key' => 'value']]))
    );
  }

  #[@test]
  public function formatting_object() {
    $this->assertEquals(
      "[log] stdClass@{\n  key => \"value\"\n}",
      $this->fixture->format($this->newEvent(LogLevel::DEBUG, [(object)['key' => 'value']]))
    );
  }

  #[@test]
  public function formatting_function() {
    $this->assertEquals(
      '[log] Test',
      $this->fixture->format($this->newEvent(LogLevel::DEBUG, [function() { return 'Test'; }]))
    );
  }

  #[@test]
  public function formatting_value() {
    $this->assertEquals(
      '[log] Test',
      $this->fixture->format($this->newEvent(LogLevel::DEBUG, [newinstance(Value::class, [], [
        'compareTo' => function($value) { return 1; },
        'hashCode'  => function() { return 'test'; },
        'toString'  => function() { return 'Test'; },
      ])]))
    );
  }
}