<?php namespace util\log\unittest;

use unittest\{Test, TestCase};
use util\collections\Vector;
use util\log\layout\PatternLayout;
use util\log\{Appender, LogCategory, LoggingEvent};

class LogAppenderTest extends TestCase {
  private $fixture, $events;

  /** @return void */
  public function setUp() {
    $this->events= create('new util.collections.Vector<string>()');
    $appender= new class($this->events) extends Appender {
      public $events;
      public function __construct($events) { $this->events= $events; }
      public function append(LoggingEvent $event) { $this->events[]= $this->layout->format($event); }
    };
    $this->fixture= (new LogCategory('default'))->withAppender($appender->withLayout(new PatternLayout('[%l] %m')));
  }
  
  #[Test]
  public function info() {
    $this->fixture->info('Hello');
    $this->assertEquals('[info] Hello', $this->events[0]);
  }

  #[Test]
  public function infoWithMultipleArguments() {
    $this->fixture->info('Hello', 'World');
    $this->assertEquals('[info] Hello World', $this->events[0]);
  }

  #[Test]
  public function warn() {
    $this->fixture->warn('Hello');
    $this->assertEquals('[warn] Hello', $this->events[0]);
  }

  #[Test]
  public function debug() {
    $this->fixture->debug('Hello');
    $this->assertEquals('[debug] Hello', $this->events[0]);
  }

  #[Test]
  public function error() {
    $this->fixture->error('Hello');
    $this->assertEquals('[error] Hello', $this->events[0]);
  }
}