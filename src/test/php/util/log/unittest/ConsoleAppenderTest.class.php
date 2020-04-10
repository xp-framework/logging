<?php namespace util\log\unittest;

use io\streams\MemoryOutputStream;
use unittest\TestCase;
use util\cmd\Console;
use util\log\{ConsoleAppender, Layout, LogCategory, LoggingEvent};

/**
 * TestCase
 *
 * @see   xp://util.cmd.Console
 * @see   xp://util.log.ConsoleAppender
 */
class ConsoleAppenderTest extends TestCase {

  /**
   * Creates a ConsoleAppender with a given target
   *
   * @param  string $target
   * @return util.log.LogCategoy
   */
  private function category($target) {
    return (new LogCategory('default'))->withAppender(
      (new ConsoleAppender($target))->withLayout(newinstance(Layout::class, [], [
        'format' => function(LoggingEvent $event) {
          return '[LOG] '.implode(' ', $event->getArguments());
        }
      ]))
    );
  }

  #[@test]
  public function can_create() {
    new ConsoleAppender();
  }

  #[@test, @values(['out', 'err', Console::$out, Console::$err])]
  public function can_create_with($target) {
    new ConsoleAppender($target);
  }

  #[@test]
  public function writes_to_stdout_by_default() {
    $this->assertEquals(Console::$out, (new ConsoleAppender())->writer());
  }

  #[@test]
  public function append_to_stderr() {
    $stream= new MemoryOutputStream();

    $err= Console::$err->getStream();
    Console::$err->setStream($stream);

    try {
      $this->category('err')->warn('Test');
      $this->assertEquals('[LOG] Test', $stream->getBytes());
    } finally {
      Console::$err->setStream($err);
    }
  }

  #[@test]
  public function append_to_stdout() {
    $stream= new MemoryOutputStream();

    $out= Console::$out->getStream();
    Console::$out->setStream($stream);

    try {
      $this->category('out')->warn('Test');
      $this->assertEquals('[LOG] Test', $stream->getBytes());
    } finally {
      Console::$out->setStream($out);
    }
  }
}