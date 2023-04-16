<?php namespace util\log\unittest;

use io\{File, Path};
use test\Assert;
use test\{Test, Values};
use util\cmd\Console;
use util\log\layout\PatternLayout;
use util\log\{ColoredConsoleAppender, ConsoleAppender, FileAppender, LogCategory, LogLevel, Logging, SyslogAppender};

class LoggingTest {

  #[Test]
  public function to() {
    $appenders= [new ColoredConsoleAppender(), new FileAppender('test.log')];
    Assert::equals($appenders, Logging::all()->to(...$appenders)->getAppenders());
  }

  #[Test]
  public function to_console() {
    Assert::instance(ColoredConsoleAppender::class, Logging::all()->toConsole()->getAppenders()[0]);
  }

  #[Test]
  public function to_console_out() {
    Assert::equals(Console::$out, Logging::all()->toConsole('out')->getAppenders()[0]->writer());
  }

  #[Test]
  public function to_console_err() {
    Assert::equals(Console::$err, Logging::all()->toConsole('err')->getAppenders()[0]->writer());
  }

  #[Test]
  public function to_console_with_colors() {
    Assert::instance(ColoredConsoleAppender::class, Logging::all()->toConsole('out', true)->getAppenders()[0]);
  }

  #[Test]
  public function to_console_without_colors() {
    Assert::instance(ConsoleAppender::class, Logging::all()->toConsole('out', false)->getAppenders()[0]);
  }

  #[Test, Values(eval: '[["test.log"], [new Path("test.log")], [new File("test.log")]]')]
  public function to_file($file) {
    Assert::instance(FileAppender::class, Logging::all()->toFile($file)->getAppenders()[0]);
  }

  #[Test]
  public function to_syslog() {
    Assert::instance(SyslogAppender::class, Logging::all()->toSyslog()->getAppenders()[0]);
  }

  #[Test]
  public function named() {
    Assert::equals('sql', Logging::named('sql')->to(new ConsoleAppender())->identifier);
  }

  #[Test]
  public function of() {
    $level= LogLevel::WARN | LogLevel::ERROR;
    Assert::equals($level, Logging::of($level)->to(new ConsoleAppender())->flags);
  }

  #[Test]
  public function using() {
    $layout= new PatternLayout('%m');
    Assert::equals($layout, Logging::using($layout)->to(new ConsoleAppender())->getAppenders()[0]->getLayout());
  }
}