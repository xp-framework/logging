<?php namespace util\log\unittest;

use test\Assert;
use test\TestCase;
use util\log\{Appender, LogCategory, LoggingEvent};

abstract class AppenderTest {

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