<?php namespace util\log\unittest;

use unittest\TestCase;
use util\Observable;
use util\log\LogCategory;
use util\log\LogObserver;

class LogObserverTest extends TestCase {

  #[@test]
  public function can_create() {
    new LogObserver(new LogCategory('test'));
  }

  #[@test]
  public function update_calls_debug() {
    $called= [];
    $l= new LogObserver(newinstance(LogCategory::class, ['test'], [
      'debug' => function(... $args) use(&$called) { $called[]= $args; }
    ]));
    $l->update(new Observable(), 'Test');

    $this->assertEquals([['Test']], $called);
  }
}