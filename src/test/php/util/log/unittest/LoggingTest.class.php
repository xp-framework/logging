<?php namespace util\log\unittest;

use util\log\Logging;
use util\log\LogCategory;
use util\log\FileAppender;
use util\log\ConsoleAppender;
use util\log\ColoredConsoleAppender;
use io\File;
use io\Path;

class LoggingTest extends \unittest\TestCase {

  #[@test]
  public function to() {
    $appenders= [new ColoredConsoleAppender(), new FileAppender('test.log')];
    $this->assertEquals($appenders, Logging::to(...$appenders)->getAppenders());
  }

  #[@test]
  public function to_console() {
    $this->assertInstanceOf(ColoredConsoleAppender::class, Logging::toConsole()->getAppenders()[0]);
  }

  #[@test]
  public function to_console_without_colors() {
    $this->assertInstanceOf(ConsoleAppender::class, Logging::toConsole(false)->getAppenders()[0]);
  }

  #[@test, @values([
  #  ['test.log'],
  #  [new Path('test.log')],
  #  [new File('test.log')]
  #])]
  public function to_file($file) {
    $this->assertInstanceOf(FileAppender::class, Logging::toFile($file)->getAppenders()[0]);
  }
}