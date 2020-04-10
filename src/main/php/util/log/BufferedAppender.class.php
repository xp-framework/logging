<?php namespace util\log;

/**
 * Appender which appends all data to a buffer
 *
 * @see   xp://util.log.Appender
 * @test  xp://net.xp_framework.unittest.logging.BufferedAppenderTest
 */  
class BufferedAppender extends Appender {
  public $buffer= '';

  /**
   * Append data
   *
   * @param   util.log.LoggingEvent event
   */ 
  public function append(LoggingEvent $event) {
    $this->buffer.= $this->layout->format($event);
  }
  
  /**
   * Get buffer's contents
   *
   * @return  string
   */
  public function getBuffer() {
    return $this->buffer;
  }
  
  /**
   * Clears the buffers content.
   *
   */
  public function clear() {
    $this->buffer= '';
  }    
}