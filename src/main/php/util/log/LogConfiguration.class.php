<?php namespace util\log;

use lang\IllegalArgumentException;
use lang\XPClass;
use util\PropertyAccess;

/**
 * Log configuration from a properties object
 *
 * ```ini
 * [default]
 * uses=console|syslog|files
 * 
 * [console]
 * class=util.log.ConsoleAppender
 * level=ALL
 * 
 * [files]
 * class=util.log.FileAppender
 * args="/var/log/server.log"
 * level=ALL
 * 
 * [syslog]
 * class=util.log.SyslogUdpAppender
 * args=127.0.0.1|514|server
 * level=WARN|ERROR
 * ```
 *
 * @test  xp://util.log.unittest.LogConfigurationTest
 */
class LogConfiguration {
  private $categories= [];

  /** Creates a new log configuration from a properties file */
  public function __construct(PropertyAccess $properties) {
    foreach ($properties->sections() as $section) {
      $cat= new LogCategory($section);
      foreach ($this->appendersFor($properties, $section) as $level => $appender) {
        $cat->addAppender($appender, $level);
      }
      $this->categories[$section]= $cat;
    }
  }

  /**
   * Returns log appenders for a given property file section
   *
   * @param  util.PropertyAccess $properties
   * @param  string $section
   * @return iterable
   */
  private function appendersFor($properties, $section) {

    // Class
    if ($class= $properties->readString($section, 'class', null)) {
      $appender= XPClass::forName($class)->newInstance(...$properties->readArray($section, 'args', []));
      if ($levels= $properties->readArray($section, 'levels', null)) {
        $level= LogLevel::NONE;
        foreach ($levels as $name) {
          $level |= LogLevel::named($name);
        }
        yield $level => $appender;
      } else {
        yield LogLevel::ALL => $appender;
      }
    }

    // Uses, referencing other section
    if ($uses= $properties->readArray($section, 'uses', null)) {
      foreach ($uses as $use) {
        foreach ($this->appendersFor($properties, $use) as $level => $appender) {
          yield $level => $appender;
        }
      }
    }
  }

  /** @return [:util.log.LogCategory] */
  public function categories() { return $this->categories; }

  /**
   * Test whether this configuration provides a log category by its name
   *
   * @param  string $name
   * @return bool
   */
  public function provides($name) {
    return isset($this->categories[$name]);
  }

  /**
   * Return a log category by its name
   *
   * @param  string $name
   * @return util.log.LogCategory
   * @throws lang.IllegalArgumentException
   */
  public function category($name) {
    if (isset($this->categories[$name])) return $this->categories[$name];

    throw new IllegalArgumentException('No log category "'.$name.'"');
  }
}