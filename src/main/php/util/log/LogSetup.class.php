<?php namespace util\log;

use util\log\layout\DefaultLayout;
use lang\Value;
use util\Objects;

/**
 * Logging DSL
 *
 * @test  xp://util.log.unittest.LoggingTest
 */
class LogSetup implements Value {
  private $category= null;
  private $level= LogLevel::ALL;
  private $layout= null;

  /**
   * Sets category
   *
   * @param  string $category
   * @return self
   */
  public function named($category) {
    $this->category= $category;
    return $this;
  }

  /**
   * Sets level
   *
   * @param  string $level
   * @return self
   */
  public function of($level) {
    $this->level= $level;
    return $this;
  }

  /**
   * Sets layout
   *
   * @param  util.log.Layout $layout
   * @return self
   */
  public function using(Layout $layout) {
    $this->layout= $layout;
    return $this;
  }

  /**
   * Returns a logging category with all specified appenders attached
   *
   * @param  util.log.Appender... $appenders
   * @return util.log.LogCategory
   */
  public function to(...$appenders) {
    $cat= new LogCategory($this->category, $this->level);
    $layout= $this->layout ?: new DefaultLayout();
    foreach ($appenders as $appender) {
      $cat->addAppender($appender->withLayout($layout));
    }
    return $cat;
  }

  /**
   * Returns a logging category with a console appender attached
   *
   * @param  bool $colors
   * @return util.log.LogCategory
   */
  public function toConsole($colors= true) {
    return self::to($colors ? new ColoredConsoleAppender() : new ConsoleAppender());
  }

  /**
   * Returns a logging category with a file appender attached
   *
   * @param  string|io.Path|io.File $file
   * @return util.log.LogCategory
   */
  public function toFile($file) {
    return self::to(new FileAppender($file));
  }

  /**
   * Returns a logging category with a syslog appender attached
   *
   * @see    php://openlog
   * @param  int $facility
   * @param  string $identifier if omitted, uses main class
   * @return util.log.LogCategory
   */
  public function toSyslog($facility= LOG_USER, $identifier= null) {
    return self::to(new SyslogAppender($identifier ?: $_SERVER['argv'][0], $facility));
  }

  /** @return string */
  public function toString() {
    return sprintf(
      '%s(category= %s, level= %s, layout= %s)',
      nameof($this),
      $this->category ?: '(default)',
      LogLevel::nameOf($this->level),
      $this->layout ? nameof($this->layout) : '(default)'
    );
  }

  /** @return string */
  public function hashCode() {
    return Objects::hashOf([$this->category, $this->level, $this->layout]);
  }

  /**
   * Compares
   *
   * @param  var $value
   * @return int
   */
  public function compareTo($value) {
    return $value instanceof self
      ? Objects::compare(
          [$this->category, $this->level, $this->layout],
          [$value->category, $value->level, $value->layout]
        )
      : 1
    ;
  }
}