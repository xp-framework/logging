<?php namespace util\log\unittest;

use io\streams\{MemoryOutputStream, OutputStream};
use test\{Assert, Test};
use util\log\layout\PatternLayout;
use util\log\{LogCategory, StreamAppender};

class StreamAppenderTest {

  /** Returns log category */
  private function category(OutputStream $out) {
    $appender= new StreamAppender($out);
    return (new LogCategory('test'))->withAppender($appender->withLayout(new PatternLayout('%l: %m%n')));
  }
  
  #[Test]
  public function debug() {
    $out= new MemoryOutputStream();
    $this->category($out)->debug('Hello');

    Assert::equals("debug: Hello\n", $out->bytes());
  }
 
  #[Test]
  public function info() {
    $out= new MemoryOutputStream();
    $this->category($out)->info('Hello');

    Assert::equals("info: Hello\n", $out->bytes());
  }

  #[Test]
  public function warn() {
    $out= new MemoryOutputStream();
    $this->category($out)->warn('Hello');

    Assert::equals("warn: Hello\n", $out->bytes());
  }

  #[Test]
  public function error() {
    $out= new MemoryOutputStream();
    $this->category($out)->error('Hello');

    Assert::equals("error: Hello\n", $out->bytes());
  }
}