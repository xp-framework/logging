<?php namespace util\log;

/**
 * Log levels
 *
 * @see   xp://util.log.Logger
 * @test  xp://net.xp_framework.unittest.logging.LogLevelTest
 */
abstract class LogLevel {
  const 
    INFO  = 0x0001,
    WARN  = 0x0002,
    ERROR = 0x0004,
    DEBUG = 0x0008;
  
  const
    NONE  = 0x0000,
    ALL   = 0x000F; // (INFO | WARN | ERROR | DEBUG)
  
  /**
   * Retrieve a loglevel by its name
   *
   * @param   string name
   * @return  int
   * @throws  lang.IllegalArgumentException
   */
  public static function named($name) {
    static $map= [
      'INFO'  => self::INFO,
      'WARN'  => self::WARN,
      'ERROR' => self::ERROR,
      'DEBUG' => self::DEBUG,
      'ALL'   => self::ALL,
      'NONE'  => self::NONE,
    ];
  
    $key= strtoupper($name);
    if (!isset($map[$key])) {
      throw new \lang\IllegalArgumentException('No such loglevel named "'.$name.'"');
    }
    return $map[$key];
  }

  /**
   * Retrieve a loglevel name for a given level
   *
   * @param   int level
   * @return  string
   * @throws  lang.IllegalArgumentException
   */
  public static function nameOf($level) {
    static $map= [
      self::INFO  => 'INFO',
      self::WARN  => 'WARN',
      self::ERROR => 'ERROR',
      self::DEBUG => 'DEBUG',
    ];
  
    if (!isset($map[$level])) {
      throw new \lang\IllegalArgumentException('No such loglevel '.$level);
    }
    return $map[$level];
  }
}