<?php namespace util\log\unittest;

use io\streams\MemoryInputStream;
use unittest\TestCase;
use util\Objects;
use util\Properties;
use util\log\ConsoleAppender;
use util\log\LogConfiguration;
use util\log\LogLevel;

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
  public function category_returns_appender_from_class() {
    $config= new LogConfiguration($this->properties('
      [default]
      class=util.log.ConsoleAppender
    '));

    $appenders= $config->category('default')->getAppenders();
    $this->assertEquals(1, sizeof($appenders), Objects::stringOf($appenders));
  }

  #[@test]
  public function category_returns_appenders_from_uses() {
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
  public function category_with_class_and_argument() {
    $config= new LogConfiguration($this->properties('
      [default]
      class=util.log.FileAppender
      args=test.log
    '));

    $appenders= $config->category('default')->getAppenders();
    $this->assertEquals('test.log', $appenders[0]->filename);
  }
}