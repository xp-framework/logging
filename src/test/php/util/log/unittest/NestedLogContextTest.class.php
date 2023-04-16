<?php namespace util\log\unittest;

use test\{Assert, Test};
use util\log\context\NestedLogContext;

class NestedLogContextTest {

  #[Test]
  public function getDepth() {
    $context= new NestedLogContext();
    Assert::equals(0, $context->getDepth());
    $context->push('val1');
    Assert::equals(1, $context->getDepth());

    $context->push('val2');
    Assert::equals(2, $context->getDepth());
  }

  #[Test]
  public function pop() {
    $context= new NestedLogContext();
    Assert::null($context->pop());
    $context->push('val1');
    $context->push('val2');
    Assert::equals('val2', $context->pop());
    Assert::equals('val1', $context->pop());
  }

  #[Test]
  public function peek() {
    $context= new NestedLogContext();
    Assert::null($context->peek());
    $context->push('val1');
    $context->push('val2');
    Assert::equals('val2', $context->peek());
    Assert::equals('val2', $context->peek());
  }

  #[Test]
  public function clear() {
    $context= new NestedLogContext();
    $context->push('val1');
    $context->push('val2');
    Assert::equals(2, $context->getDepth());
    $context->clear();
    Assert::equals(0, $context->getDepth());
  }

  #[Test]
  public function format() {
    $context= new NestedLogContext();
    Assert::equals('', $context->format());
    $context->push('val1');
    $context->push('val2');
    Assert::equals('val1 val2', $context->format());
  }

  #[Test]
  public function toStringTest() {
    $context= new NestedLogContext();
    Assert::equals('util.log.context.NestedLogContext{}', $context->toString());
    $context->push('val1');
    $context->push('val2');
    Assert::equals('util.log.context.NestedLogContext{val1 > val2}', $context->toString());
  }
}