<?php namespace util\log;

use lang\{FormatException, IllegalArgumentException, Throwable, XPClass};
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

  /**
   * Creates a new log configuration from a properties file
   *
   * @param  util.PropertyAccess $properties
   * @throws lang.FormatException if the property file contains errors
   */
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
   * Creates a new log appender from a class and arguments, which are either
   * a list matching the constructor argument order or a map of named arguments.
   *
   * @param  lang.XPClass $class
   * @param  var[]|[:var] $args
   * @return util.log.LogAppender
   * @throws lang.FormatException
   */
  private function newAppender($class, $args) {
    if (null === ($c= $class->getConstructor())) {
      return $class->newInstance();
    } else if (0 === key($args)) {
      return $c->newInstance($args);
    } else {
      $pass= [];
      foreach ($c->getParameters() as $param) {
        $name= $param->getName();
        $pass[]= isset($args[$name]) ? $args[$name] : $param->getDefaultValue();
        unset($args[$name]);
      }
      if ($args) {
        throw new FormatException('Unknown named argument(s) '.implode(', ', array_keys($args)));
      }
      return $c->newInstance($pass);
    }
  }

  /**
   * Returns log appenders for a given property file section
   *
   * @param  util.PropertyAccess $properties
   * @param  string $section
   * @return iterable
   * @throws lang.FormatException
   */
  private function appendersFor($properties, $section) {
    static $names= [
      'INFO'  => LogLevel::INFO,
      'WARN'  => LogLevel::WARN,
      'ERROR' => LogLevel::ERROR,
      'DEBUG' => LogLevel::DEBUG,
      'ALL'   => LogLevel::ALL,
      'NONE'  => LogLevel::NONE,
    ];

    // Class
    if ($class= $properties->readString($section, 'class', null)) {
      try {
        $appender= $this->newAppender(XPClass::forName($class), $properties->readArray($section, 'args', []));
        if ($layout= $properties->readString($section, 'layout', null)) {
          if (false !== ($p= strcspn($layout, '('))) {
            $appender->setLayout(XPClass::forName(substr($layout, 0, $p))->newInstance(...eval(
              'return ['.substr($layout, $p + 1, -1).'];'
            )));
          } else {
            $appender->setLayout(XPClass::forName($layout)->newInstance());
          }
        }
      } catch (Throwable $e) {
        throw new FormatException('Class '.$class.' in section "'.$section.'" cannot be instantiated', $e);
      }

      if ($levels= $properties->readArray($section, 'level', null)) {
        $level= LogLevel::NONE;
        foreach ($levels as $name) {
          if (!isset($names[$name])) {
            throw new FormatException('Level '.$name.' in section "'.$section.'" not recognized');
          }
          $level |= $names[$name];
        }
        yield $level => $appender;
      } else {
        yield LogLevel::ALL => $appender;
      }
    }

    // Uses, referencing other section
    if ($uses= $properties->readArray($section, 'uses', null)) {
      foreach ($uses as $use) {
        if (!$properties->hasSection($use)) {
          throw new FormatException('Uses in section "'.$section.'" references non-existant section "'.$use.'"');
        }
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