<?php namespace util\log;

use io\streams\StringWriter;
use lang\IllegalArgumentException;
use util\cmd\Console;

/**
 * Appender which appends data to console. The data goes to STDERR.
 *
 * Note: STDERR will not be defined in a web server's environment,
 * so using this class will have no effect - have a look at the
 * SyslogAppender or FileAppender classes instead.
 *
 * @test    xp://net.xp_framework.unittest.logging.ConsoleAppenderTest
 * @see     xp://util.log.Appender
 */  
class ConsoleAppender extends Appender {
  protected $writer= null;

  /**
   * Constructor
   *
   * @param  string|io.streams.StringWriter $target
   * @throws lang.IllegalArgumentException
   */
  public function __construct($target= 'out') {
    if ($target instanceof StringWriter) {
      $this->writer= $target;
    } else if ('out' === $target) {
      $this->writer= Console::$out;
    } else if ('err' === $target) {
      $this->writer= Console::$err;
    } else {
      throw new IllegalArgumentException('Expected either "out", "err" or an io.streams.StringWriter instance');
    }
  }

  /** @return io.streams.StringWriter */
  public function writer() { return $this->writer; }
  
  /**
   * Append data
   *
   * @param  util.log.LoggingEvent event
   * @return void
   */ 
  public function append(LoggingEvent $event) {
    $this->writer->write($this->layout->format($event));
  }
}