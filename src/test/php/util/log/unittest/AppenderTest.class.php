<?php namespace util\log\unittest;

use unittest\TestCase;
use util\log\{Appender, LogCategory, LoggingEvent};

abstract class AppenderTest extends TestCase {

  /**
   * Creates new logging event
   *
   * @param   int level see util.log.LogLevel
   * @param   string message
   * @return  util.log.LoggingEvent
   */
  protected function newEvent($level, $message) {
    return new LoggingEvent(new LogCategory('test'), 0, 0, $level, [$message]);
  }
}