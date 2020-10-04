<?php namespace util\log\unittest;

use unittest\Test;
use util\log\context\MappedLogContext;

/**
 * Tests MappedLogContext class
 */
class MappedLogContextTest extends \unittest\TestCase {
  private $context;

  /** @return void */
  public function setUp() {
    $this->context= new MappedLogContext();
  }

  #[Test]
  public function hasKey() {
    $this->assertFalse($this->context->hasKey('key1'));
    $this->context->put('key1', 'val1');
    $this->assertTrue($this->context->hasKey('key1'));

    $this->assertFalse($this->context->hasKey('key2'));
    $this->context->put('key2', 'val2');
    $this->assertTrue($this->context->hasKey('key2'));
  }

  #[Test]
  public function get() {
    $this->assertNull($this->context->get('key1'));
    $this->context->put('key1', 'val1');
    $this->assertEquals('val1', $this->context->get('key1'));

    $this->assertNull($this->context->get('key2'));
    $this->context->put('key2', 'val2');
    $this->assertEquals('val2', $this->context->get('key2'));
  }

  #[Test]
  public function remove() {
    $this->context->put('key1', 'val1');
    $this->assertEquals('val1', $this->context->get('key1'));
    $this->context->remove('key1');
    $this->assertNull($this->context->get('key1'));
  }

  #[Test]
  public function removeUnexistingKey() {
    $this->context->remove('unexistingKey');
  }

  #[Test]
  public function clear() {
    $this->context->put('key1', 'val1');
    $this->context->put('key2', 'val2');
    $this->context->clear();
    $this->assertFalse($this->context->hasKey('key1'));
    $this->assertFalse($this->context->hasKey('key2'));
  }

  #[Test]
  public function format() {
    $this->assertEquals('', $this->context->format());
    $this->context->put('key1', 'val1');
    $this->context->put('key2', 'val2');
    $this->assertEquals('key1=val1 key2=val2', $this->context->format());
  }

  #[Test]
  public function toStringTest() {
    $this->assertEquals('util.log.context.MappedLogContext{}', $this->context->toString());
    $this->context->put('key1', 'val1');
    $this->context->put('key2', 'val2');
    $this->assertEquals(
      "util.log.context.MappedLogContext{\n  key1=val1\n  key2=val2\n}",
      $this->context->toString()
    );
  }
}