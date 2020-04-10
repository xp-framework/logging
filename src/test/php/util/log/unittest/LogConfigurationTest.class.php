<?php namespace util\log\unittest;

use io\streams\MemoryInputStream;
use lang\{ClassLoader, FormatException, IllegalArgumentException};
use unittest\TestCase;
use util\{Objects, Properties};
use util\log\{Appender, ConsoleAppender, FileAppender, LogConfiguration, LogLevel, LoggingEvent};
use util\log\layout\PatternLayout;

class LogConfigurationTest extends TestCase {

  /**
   * Creates a Properties object from a string
   *
   * @param  string $properties
   * @return util.Properties
   */
  private function properties($properties) {
    $p= new Properties(null);
    $p->load(new MemoryInputStream(trim($properties)));
    return $p;
  }

  #[@test]
  public function can_create() {
    new LogConfiguration($this->properties(''));
  }

  #[@test]
  public function categories_for_empty_file() {
    $config= new LogConfiguration($this->properties(''));
    $this->assertEquals([], $config->categories());
  }

  #[@test]
  public function categories() {
    $config= new LogConfiguration($this->properties('
      [default]
      class=util.log.ConsoleAppender
    '));

    $this->assertInstanceOf('[:util.log.LogCategory]', $config->categories());
  }

  #[@test, @values([
  #  ['default', true],
  #  ['files', false],
  #])]
  public function provides($name, $expected) {
    $config= new LogConfiguration($this->properties('
      [default]
      class=util.log.ConsoleAppender
    '));
    $this->assertEquals($expected, $config->provides($name));
  }

  #[@test]
  public function appender_configured_via_class() {
    $config= new LogConfiguration($this->properties('
      [default]
      class=util.log.ConsoleAppender
    '));

    $appenders= $config->category('default')->getAppenders();
    $this->assertEquals(1, sizeof($appenders), Objects::stringOf($appenders));
  }

  #[@test]
  public function appenders_referenced_via_uses() {
    $config= new LogConfiguration($this->properties('
      [default]
      uses=console|files

      [console]
      class=util.log.ConsoleAppender

      [files]
      class=util.log.FileAppender
      args=test.log
    '));

    $appenders= $config->category('default')->getAppenders();
    $this->assertEquals(2, sizeof($appenders), Objects::stringOf($appenders));
  }

  #[@test]
  public function uses_can_be_nested() {
    $config= new LogConfiguration($this->properties('
      [default]
      uses=tee

      [tee]
      class=util.log.ConsoleAppender
      uses=syslog|files

      [syslog]
      class=util.log.SyslogAppender

      [files]
      class=util.log.FileAppender
      args=test.log
    '));

    $appenders= $config->category('default')->getAppenders();
    $this->assertEquals(3, sizeof($appenders), Objects::stringOf($appenders));
  }

  #[@test, @expect(
  #  class= FormatException::class,
  #  withMessage= 'Uses in section "default" references non-existant section "missing"'
  #)]
  public function uses_referencing_non_existant_section() {
    new LogConfiguration($this->properties('
      [default]
      uses=console|missing

      [console]
      class=util.log.ConsoleAppender
    '));
  }

  #[@test, @expect(
  #  class= FormatException::class,
  #  withMessage= 'Class util.log.NonExistantAppender in section "default" cannot be instantiated'
  #)]
  public function non_existant_appender() {
    new LogConfiguration($this->properties('
      [default]
      class=util.log.NonExistantAppender
    '));
  }

  #[@test, @expect(
  #  class= FormatException::class,
  #  withMessage= 'Class util.log.ConsoleAppender in section "default" cannot be instantiated'
  #)]
  public function exceptions_when_instantiating_appenders() {
    new LogConfiguration($this->properties('
      [default]
      class=util.log.ConsoleAppender
      args=STDIN
    '));
  }

  #[@test, @expect(
  #  class= FormatException::class,
  #  withMessage= 'Level TEST in section "default" not recognized'
  #)]
  public function non_existant_level() {
    new LogConfiguration($this->properties('
      [default]
      class=util.log.ConsoleAppender
      level=TEST
    '));
  }

  #[@test, @expect(
  #  class= IllegalArgumentException::class,
  #  withMessage= 'No log category "default"'
  #)]
  public function missing_category() {
    $config= new LogConfiguration($this->properties(''));
    $config->category('default');
  }

  #[@test]
  public function category_with_class_and_argument() {
    $config= new LogConfiguration($this->properties('
      [default]
      class=util.log.FileAppender
      args=test.log
    '));

    $appenders= $config->category('default')->getAppenders();
    $this->assertEquals('test.log', $appenders[0]->filename);
  }

  #[@test]
  public function category_with_class_and_array_argument() {
    $config= new LogConfiguration($this->properties('
      [default]
      class=util.log.FileAppender
      args[]=test.log
    '));

    $appenders= $config->category('default')->getAppenders();
    $this->assertEquals('test.log', $appenders[0]->filename);
  }

  #[@test, @expect(FormatException::class)]
  public function category_with_class_and_missing_argument() {
    ClassLoader::defineClass('util.log.unittest.LogConfigurationTest_Appender', Appender::class, [], [
      '__construct' => function($arg) { /* ... */ },
      'append' => function(LoggingEvent $event) { /* ... */ },
    ]);
    $config= new LogConfiguration($this->properties('
      [default]
      class=util.log.unittest.LogConfigurationTest_Appender
    '));

    $config->category('default')->getAppenders();
  }

  #[@test]
  public function category_with_class_and_named_argument() {
    $config= new LogConfiguration($this->properties('
      [default]
      class=util.log.FileAppender
      args[file]=test.log
    '));

    $appenders= $config->category('default')->getAppenders();
    $this->assertEquals('test.log', $appenders[0]->filename);
  }

  #[@test]
  public function category_with_class_and_named_arguments() {
    $config= new LogConfiguration($this->properties('
      [default]
      class=util.log.SyslogUdpAppender
      args[ip]=127.0.0.1
      args[identifier]=test
    '));

    $appenders= $config->category('default')->getAppenders();
    $this->assertEquals('127.0.0.1:514', $appenders[0]->ip.':'.$appenders[0]->port);
  }

  #[@test, @expect(FormatException::class)]
  public function category_with_non_existant_named_argument() {
    $config= new LogConfiguration($this->properties('
      [default]
      class=util.log.SyslogUdpAppender
      args[non-existant-parameter]=***
    '));

    $config->category('default')->getAppenders();
  }

  #[@test]
  public function categories_with_loglevels() {
    $config= new LogConfiguration($this->properties('
      [default]
      uses=console|files

      [console]
      class=util.log.ConsoleAppender
      level=INFO

      [files]
      class=util.log.FileAppender
      args=test.log
      level=ERROR
    '));

    $cat= $config->category('default');
    $this->assertInstanceOf(ConsoleAppender::class, $cat->getAppenders(LogLevel::INFO)[0]);
    $this->assertInstanceOf(FileAppender::class, $cat->getAppenders(LogLevel::ERROR)[0]);
  }

  #[@test]
  public function category_with_class_and_layout() {
    $config= new LogConfiguration($this->properties('
      [default]
      class=util.log.SyslogUdpAppender
      layout=util.log.layout.PatternLayout("%c - %m")
    '));

    $appenders= $config->category('default')->getAppenders();
    $this->assertEquals(new PatternLayout('%c - %m'), $appenders[0]->getLayout());
  }

  #[@test, @expect(FormatException::class)]
  public function category_with_class_and_non_existant_layout() {
    $config= new LogConfiguration($this->properties('
      [default]
      class=util.log.SyslogUdpAppender
      layout=util.log.layout.NonExistantLayout
    '));

    $config->category('default')->getAppenders();
  }
}