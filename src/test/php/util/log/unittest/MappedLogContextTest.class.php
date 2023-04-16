<?php namespace util\log\unittest;

use test\{Assert, Test};
use util\log\context\MappedLogContext;

class MappedLogContextTest {

  #[Test]
  public function hasKey() {
    $context= new MappedLogContext();
    Assert::false($context->hasKey('key1'));
    $context->put('key1', 'val1');
    Assert::true($context->hasKey('key1'));

    Assert::false($context->hasKey('key2'));
    $context->put('key2', 'val2');
    Assert::true($context->hasKey('key2'));
  }

  #[Test]
  public function get() {
    $context= new MappedLogContext();
    Assert::null($context->get('key1'));
    $context->put('key1', 'val1');
    Assert::equals('val1', $context->get('key1'));

    Assert::null($context->get('key2'));
    $context->put('key2', 'val2');
    Assert::equals('val2', $context->get('key2'));
  }

  #[Test]
  public function remove() {
    $context= new MappedLogContext();
    $context->put('key1', 'val1');
    Assert::equals('val1', $context->get('key1'));
    $context->remove('key1');
    Assert::null($context->get('key1'));
  }

  #[Test]
  public function removeUnexistingKey() {
    $context= new MappedLogContext();
    $context->remove('unexistingKey');
  }

  #[Test]
  public function clear() {
    $context= new MappedLogContext();
    $context->put('key1', 'val1');
    $context->put('key2', 'val2');
    $context->clear();
    Assert::false($context->hasKey('key1'));
    Assert::false($context->hasKey('key2'));
  }

  #[Test]
  public function format() {
    $context= new MappedLogContext();
    Assert::equals('', $context->format());
    $context->put('key1', 'val1');
    $context->put('key2', 'val2');
    Assert::equals('key1=val1 key2=val2', $context->format());
  }

  #[Test]
  public function toStringTest() {
    $context= new MappedLogContext();
    Assert::equals('util.log.context.MappedLogContext{}', $context->toString());
    $context->put('key1', 'val1');
    $context->put('key2', 'val2');
    Assert::equals(
      "util.log.context.MappedLogContext{\n  key1=val1\n  key2=val2\n}",
      $context->toString()
    );
  }
}