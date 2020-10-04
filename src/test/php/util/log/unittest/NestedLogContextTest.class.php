<?php namespace util\log\unittest;

use unittest\{Test, TestCase};
use util\log\context\NestedLogContext;

class NestedLogContextTest extends TestCase {
  private $context;

  /** @return void */
  public function setUp() {
    $this->context= new NestedLogContext();
  }

  #[Test]
  public function getDepth() {
    $this->assertEquals(0, $this->context->getDepth());
    $this->context->push('val1');
    $this->assertEquals(1, $this->context->getDepth());

    $this->context->push('val2');
    $this->assertEquals(2, $this->context->getDepth());
  }

  #[Test]
  public function pop() {
    $this->assertNull($this->context->pop());
    $this->context->push('val1');
    $this->context->push('val2');
    $this->assertEquals('val2', $this->context->pop());
    $this->assertEquals('val1', $this->context->pop());
  }

  #[Test]
  public function peek() {
    $this->assertNull($this->context->peek());
    $this->context->push('val1');
    $this->context->push('val2');
    $this->assertEquals('val2', $this->context->peek());
    $this->assertEquals('val2', $this->context->peek());
  }

  #[Test]
  public function clear() {
    $this->context->push('val1');
    $this->context->push('val2');
    $this->assertEquals(2, $this->context->getDepth());
    $this->context->clear();
    $this->assertEquals(0, $this->context->getDepth());
  }

  #[Test]
  public function format() {
    $this->assertEquals('', $this->context->format());
    $this->context->push('val1');
    $this->context->push('val2');
    $this->assertEquals('val1 val2', $this->context->format());
  }

  #[Test]
  public function toStringTest() {
    $this->assertEquals('util.log.context.NestedLogContext{}', $this->context->toString());
    $this->context->push('val1');
    $this->context->push('val2');
    $this->assertEquals('util.log.context.NestedLogContext{val1 > val2}', $this->context->toString());
  }
}