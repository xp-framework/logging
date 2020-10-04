<?php namespace util\log\unittest;

use io\streams\MemoryOutputStream;
use unittest\{Test, TestCase};
use util\log\layout\PatternLayout;
use util\log\{LogCategory, StreamAppender};

class StreamAppenderTest extends TestCase {
  private $out, $cat;

  /**
   * Sets up test case
   *
   */
  public function setUp() {
    $this->out= new MemoryOutputStream();
    $this->cat= (new LogCategory('default'))->withAppender(
      (new StreamAppender($this->out))->withLayout(new PatternLayout('%l: %m%n'))
    );
  }
  
  #[Test]
  public function debug() {
    $this->cat->debug('Hello');
    $this->assertEquals("debug: Hello\n", $this->out->bytes());
  }
 
  #[Test]
  public function info() {
    $this->cat->info('Hello');
    $this->assertEquals("info: Hello\n", $this->out->bytes());
  }

  #[Test]
  public function warn() {
    $this->cat->warn('Hello');
    $this->assertEquals("warn: Hello\n", $this->out->bytes());
  }

  #[Test]
  public function error() {
    $this->cat->error('Hello');
    $this->assertEquals("error: Hello\n", $this->out->bytes());
  }
}