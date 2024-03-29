<?php namespace util\log\unittest;

use test\Assert;
use test\{Test, TestCase};
use util\Observable;
use util\log\{LogCategory, LogObserver};

class LogObserverTest {

  #[Test]
  public function can_create() {
    new LogObserver(new LogCategory('test'));
  }

  #[Test]
  public function update_calls_debug() {
    $called= [];
    $l= new LogObserver(newinstance(LogCategory::class, ['test'], [
      'debug' => function(... $args) use(&$called) { $called[]= $args; }
    ]));
    $l->update(new Observable(), 'Test');

    Assert::equals([['Test']], $called);
  }
}