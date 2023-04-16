<?php namespace util\log\unittest;

use test\{Assert, Test};
use util\log\layout\PatternLayout;
use util\log\{Appender, LogCategory, LoggingEvent};

class LogAppenderTest {

  /** Returns log category */
  private function category() {
    $appender= new class() extends Appender {
      public $events= [];
      public function append(LoggingEvent $event) { $this->events[]= $this->layout->format($event); }
    };
    return (new LogCategory('test'))->withAppender($appender->withLayout(new PatternLayout('[%l] %m')));
  }

  #[Test]
  public function info() {
    $cat= $this->category();
    $cat->info('Hello');
    Assert::equals('[info] Hello', $cat->getAppenders()[0]->events[0]);
  }

  #[Test]
  public function infoWithMultipleArguments() {
    $cat= $this->category();
    $cat->info('Hello', 'World');
    Assert::equals('[info] Hello World', $cat->getAppenders()[0]->events[0]);
  }

  #[Test]
  public function warn() {
    $cat= $this->category();
    $cat->warn('Hello');
    Assert::equals('[warn] Hello', $cat->getAppenders()[0]->events[0]);
  }

  #[Test]
  public function debug() {
    $cat= $this->category();
    $cat->debug('Hello');
    Assert::equals('[debug] Hello', $cat->getAppenders()[0]->events[0]);
  }

  #[Test]
  public function error() {
    $cat= $this->category();
    $cat->error('Hello');
    Assert::equals('[error] Hello', $cat->getAppenders()[0]->events[0]);
  }
}