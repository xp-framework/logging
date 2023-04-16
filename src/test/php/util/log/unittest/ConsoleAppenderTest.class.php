<?php namespace util\log\unittest;

use io\streams\MemoryOutputStream;
use test\Assert;
use test\{Test, TestCase, Values};
use util\cmd\Console;
use util\log\{ConsoleAppender, Layout, LogCategory, LoggingEvent};

/**
 * TestCase
 *
 * @see   xp://util.cmd.Console
 * @see   xp://util.log.ConsoleAppender
 */
class ConsoleAppenderTest {

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

  #[Test]
  public function can_create() {
    new ConsoleAppender();
  }

  #[Test, Values(eval: '["out", "err", Console::$out, Console::$err]')]
  public function can_create_with($target) {
    new ConsoleAppender($target);
  }

  #[Test]
  public function writes_to_stdout_by_default() {
    Assert::equals(Console::$out, (new ConsoleAppender())->writer());
  }

  #[Test]
  public function append_to_stderr() {
    $stream= new MemoryOutputStream();

    $err= Console::$err->stream();
    Console::$err->redirect($stream);

    try {
      $this->category('err')->warn('Test');
      Assert::equals('[LOG] Test', $stream->bytes());
    } finally {
      Console::$err->redirect($err);
    }
  }

  #[Test]
  public function append_to_stdout() {
    $stream= new MemoryOutputStream();

    $out= Console::$out->stream();
    Console::$out->redirect($stream);

    try {
      $this->category('out')->warn('Test');
      Assert::equals('[LOG] Test', $stream->bytes());
    } finally {
      Console::$out->redirect($out);
    }
  }
}