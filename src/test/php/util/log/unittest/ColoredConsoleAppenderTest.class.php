<?php namespace util\log\unittest;

use io\streams\{MemoryOutputStream, StringWriter};
use test\Assert;
use test\{Test, TestCase, Values};
use util\cmd\Console;
use util\log\{ColoredConsoleAppender, Layout, LogCategory, LogLevel, LoggingEvent};

class ColoredConsoleAppenderTest {

  /**
   * Creates a ColoredConsoleAppender with a given target
   *
   * @param  string|io.streams.OutputStream $target
   * @return util.log.LogCategoy
   */
  private function category($target) {
    return (new LogCategory('test'))->withAppender(
      (new ColoredConsoleAppender(new StringWriter($target)))->withLayout(newinstance(Layout::class, [], [
        'format' => function(LoggingEvent $event) {
          return '[LOG] '.implode(' ', $event->getArguments());
        }
      ]))
    );
  }

  #[Test]
  public function can_create() {
    new ColoredConsoleAppender();
  }

  #[Test]
  public function writes_to_stdout_by_default() {
    Assert::equals(Console::$out, (new ColoredConsoleAppender())->writer());
  }

  #[Test]
  public function can_overwrite_colors() {
    Assert::equals('00;30', (new ColoredConsoleAppender('out', [LogLevel::INFO => '00;30']))->colors()[LogLevel::INFO]);
  }

  #[Test, Values(eval: '[["out", Console::$out], ["err", Console::$err]]')]
  public function writes_to($param, $writer) {
    Assert::equals($writer, (new ColoredConsoleAppender($param))->writer());
  }

  #[Test]
  public function info() {
    $out= new MemoryOutputStream();
    $this->category($out)->info('Test');

    Assert::equals("\033[00;00m[LOG] Test\033[0m", $out->bytes());
  }

  #[Test]
  public function warn() {
    $out= new MemoryOutputStream();
    $this->category($out)->warn('Test');

    Assert::equals("\033[00;31m[LOG] Test\033[0m", $out->bytes());
  }

  #[Test]
  public function error() {
    $out= new MemoryOutputStream();
    $this->category($out)->error('Test');

    Assert::equals("\033[01;31m[LOG] Test\033[0m", $out->bytes());
  }

  #[Test]
  public function debug() {
    $out= new MemoryOutputStream();
    $this->category($out)->debug('Test');

    Assert::equals("\033[00;34m[LOG] Test\033[0m", $out->bytes());
  }
}