<?php namespace util\log\unittest;

use lang\Value;
use test\{After, Assert, Before, Test, Values};
use util\log\{Layout, LogCategory, LogLevel, LoggingEvent};

class LayoutTest {
  private $fixture, $tz;

  #[Before]
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

    // Save timezone, it will be restored inside tearDown()
    $this->tz= date_default_timezone_get();
    date_default_timezone_set('Europe/Berlin');
  }

  #[After]
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
  private function newEvent($level, $args) {
    return new LoggingEvent(new LogCategory('test'), 0, 0, $level, $args);
  }

  #[Test, Values([[null, 'null'], [true, 'true'], [false, 'false'], [1, '1'], [1.5, '1.5'], ['Test', 'Test'],])]
  public function formatting_scalars($value, $expected) {
    Assert::equals(
      '[log] '.$expected,
      $this->fixture->format($this->newEvent(LogLevel::DEBUG, [$value]))
    );
  }

  #[Test, Values([[[], '[]'], [[1], '[1]'], [[1, 2, 3], '[1, 2, 3]'], [[[1]], '[[1]]'],])]
  public function formatting_arrays($value, $expected) {
    Assert::equals(
      '[log] '.$expected,
      $this->fixture->format($this->newEvent(LogLevel::DEBUG, [$value]))
    );
  }

  #[Test]
  public function formatting_map() {
    Assert::equals(
      "[log] [\n  key => \"value\"\n]",
      $this->fixture->format($this->newEvent(LogLevel::DEBUG, [['key' => 'value']]))
    );
  }

  #[Test]
  public function formatting_object() {
    Assert::equals(
      "[log] stdClass@{\n  key => \"value\"\n}",
      $this->fixture->format($this->newEvent(LogLevel::DEBUG, [(object)['key' => 'value']]))
    );
  }

  #[Test]
  public function formatting_function() {
    Assert::equals(
      '[log] Test',
      $this->fixture->format($this->newEvent(LogLevel::DEBUG, [function() { return 'Test'; }]))
    );
  }

  #[Test]
  public function formatting_value() {
    Assert::equals(
      '[log] Test',
      $this->fixture->format($this->newEvent(LogLevel::DEBUG, [newinstance(Value::class, [], [
        'compareTo' => function($value) { return 1; },
        'hashCode'  => function() { return 'test'; },
        'toString'  => function() { return 'Test'; },
      ])]))
    );
  }
}