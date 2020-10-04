<?php namespace util\log\unittest;

use io\{File, Path};
use unittest\{Test, Values};
use util\cmd\Console;
use util\log\layout\PatternLayout;
use util\log\{ColoredConsoleAppender, ConsoleAppender, FileAppender, LogCategory, LogLevel, Logging, SyslogAppender};

class LoggingTest extends \unittest\TestCase {

  #[Test]
  public function to() {
    $appenders= [new ColoredConsoleAppender(), new FileAppender('test.log')];
    $this->assertEquals($appenders, Logging::all()->to(...$appenders)->getAppenders());
  }

  #[Test]
  public function to_console() {
    $this->assertInstanceOf(ColoredConsoleAppender::class, Logging::all()->toConsole()->getAppenders()[0]);
  }

  #[Test]
  public function to_console_out() {
    $this->assertEquals(Console::$out, Logging::all()->toConsole('out')->getAppenders()[0]->writer());
  }

  #[Test]
  public function to_console_err() {
    $this->assertEquals(Console::$err, Logging::all()->toConsole('err')->getAppenders()[0]->writer());
  }

  #[Test]
  public function to_console_with_colors() {
    $this->assertInstanceOf(ColoredConsoleAppender::class, Logging::all()->toConsole('out', true)->getAppenders()[0]);
  }

  #[Test]
  public function to_console_without_colors() {
    $this->assertInstanceOf(ConsoleAppender::class, Logging::all()->toConsole('out', false)->getAppenders()[0]);
  }

  #[Test, Values(eval: '[["test.log"], [new Path("test.log")], [new File("test.log")]]')]
  public function to_file($file) {
    $this->assertInstanceOf(FileAppender::class, Logging::all()->toFile($file)->getAppenders()[0]);
  }

  #[Test]
  public function to_syslog() {
    $this->assertInstanceOf(SyslogAppender::class, Logging::all()->toSyslog()->getAppenders()[0]);
  }

  #[Test]
  public function named() {
    $this->assertEquals('sql', Logging::named('sql')->to(new ConsoleAppender())->identifier);
  }

  #[Test]
  public function of() {
    $level= LogLevel::WARN | LogLevel::ERROR;
    $this->assertEquals($level, Logging::of($level)->to(new ConsoleAppender())->flags);
  }

  #[Test]
  public function using() {
    $layout= new PatternLayout('%m');
    $this->assertEquals($layout, Logging::using($layout)->to(new ConsoleAppender())->getAppenders()[0]->getLayout());
  }
}