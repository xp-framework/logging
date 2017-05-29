<?php namespace util\log;

/**
 * Logging DSL
 *
 * @test  xp://util.log.unittest.LoggingTest
 */
abstract class Logging {

  /**
   * Returns a logging setup with all loglevels
   *
   * @return util.log.LogSetup
   */
  public static function all() {
    return new LogSetup();
  }

  /**
   * Returns a logging setup with a given category
   *
   * @param  string $category
   * @return util.log.LogSetup
   */
  public static function named($category) {
    return (new LogSetup())->named($category);
  }

  /**
   * Returns a logging setup with a given log level
   *
   * @param  int $level
   * @return util.log.LogSetup
   */
  public static function of($level) {
    return (new LogSetup())->of($level);
  }

  /**
   * Returns a logging setup with a given layout
   *
   * @param  util.log.Layout $layout
   * @return util.log.LogSetup
   */
  public static function using(Layout $layout) {
    return (new LogSetup())->using($layout);
  }
}