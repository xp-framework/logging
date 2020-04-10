<?php namespace util\log\unittest;

use io\{File, Path};
use util\cmd\Console;
use util\log\{ColoredConsoleAppender, ConsoleAppender, FileAppender, LogCategory, LogLevel, Logging, SyslogAppender};
use util\log\layout\PatternLayout;

class LoggingTest extends \unittest\TestCase {

  #[@test]
  public function to() {
    $appenders= [new ColoredConsoleAppender(), new FileAppender('test.log')];
    $this->assertEquals($appenders, Logging::all()->to(...$appenders)->getAppenders());
  }

  #[@test]
  public function to_console() {
    $this->assertInstanceOf(ColoredConsoleAppender::class, Logging::all()->toConsole()->getAppenders()[0]);
  }

  #[@test]
  public function to_console_out() {
    $this->assertEquals(Console::$out, Logging::all()->toConsole('out')->getAppenders()[0]->writer());
  }

  #[@test]
  public function to_console_err() {
    $this->assertEquals(Console::$err, Logging::all()->toConsole('err')->getAppenders()[0]->writer());
  }

  #[@test]
  public function to_console_with_colors() {
    $this->assertInstanceOf(ColoredConsoleAppender::class, Logging::all()->toConsole('out', true)->getAppenders()[0]);
  }

  #[@test]
  public function to_console_without_colors() {
    $this->assertInstanceOf(ConsoleAppender::class, Logging::all()->toConsole('out', false)->getAppenders()[0]);
  }

  #[@test, @values([
  #  ['test.log'],
  #  [new Path('test.log')],
  #  [new File('test.log')]
  #])]
  public function to_file($file) {
    $this->assertInstanceOf(FileAppender::class, Logging::all()->toFile($file)->getAppenders()[0]);
  }

  #[@test]
  public function to_syslog() {
    $this->assertInstanceOf(SyslogAppender::class, Logging::all()->toSyslog()->getAppenders()[0]);
  }

  #[@test]
  public function named() {
    $this->assertEquals('sql', Logging::named('sql')->to(new ConsoleAppender())->identifier);
  }

  #[@test]
  public function of() {
    $level= LogLevel::WARN | LogLevel::ERROR;
    $this->assertEquals($level, Logging::of($level)->to(new ConsoleAppender())->flags);
  }

  #[@test]
  public function using() {
    $layout= new PatternLayout('%m');
    $this->assertEquals($layout, Logging::using($layout)->to(new ConsoleAppender())->getAppenders()[0]->getLayout());
  }
}