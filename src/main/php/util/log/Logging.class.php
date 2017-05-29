<?php namespace util\log;

use io\File;
use io\Path;

abstract class Logging {

  /**
   * Returns a logging category with all specified appenders attached
   *
   * @param  util.log.Appender... $appenders
   * @return util.log.LogCategory
   */
  public static function to(...$appenders) {
    $cat= new LogCategory();
    foreach ($appenders as $appender) {
      $cat->addAppender($appender);
    }
    return $cat;
  }

  /**
   * Returns a logging category with a console appender attached
   *
   * @param  bool $colors
   * @return util.log.LogCategory
   */
  public static function toConsole($colors= true) {
    return self::to($colors ? new ColoredConsoleAppender() : new ConsoleAppender());
  }

  /**
   * Returns a logging category with a file appender attached
   *
   * @param  string|io.Path|io.File $file
   * @return util.log.LogCategory
   */
  public static function toFile($file) {
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
  public static function toSyslog($facility= LOG_USER, $identifier= null) {
    return self::to(new SyslogAppender($identifier ?: $_SERVER['argv'][0], $facility));
  }
}