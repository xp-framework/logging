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
   * @return util.log.LogCategory
   */
  public static function toConsole() {
    return self::to(new ColoredConsoleAppender());
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
}