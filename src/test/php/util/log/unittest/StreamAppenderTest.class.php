<?php namespace util\log\unittest;

use io\streams\MemoryOutputStream;
use unittest\TestCase;
use util\log\{LogCategory, StreamAppender};
use util\log\layout\PatternLayout;

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
  
  #[@test]
  public function debug() {
    $this->cat->debug('Hello');
    $this->assertEquals("debug: Hello\n", $this->out->getBytes());
  }
 
  #[@test]
  public function info() {
    $this->cat->info('Hello');
    $this->assertEquals("info: Hello\n", $this->out->getBytes());
  }

  #[@test]
  public function warn() {
    $this->cat->warn('Hello');
    $this->assertEquals("warn: Hello\n", $this->out->getBytes());
  }

  #[@test]
  public function error() {
    $this->cat->error('Hello');
    $this->assertEquals("error: Hello\n", $this->out->getBytes());
  }
}