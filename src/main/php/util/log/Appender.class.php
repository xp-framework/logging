<?php namespace util\log;

use lang\Value;
use util\Objects;

/**
 * Abstract base class for appenders
 *
 * @see   xp://util.log.LogCategory#addAppender
 */
abstract class Appender implements Value {
  private static $id= 0;
  private $__id= null;

  protected $layout= null;

  /**
   * Sets layout
   *
   * @param   util.log.Layout layout
   */
  public function setLayout(Layout $layout) {
    $this->layout= $layout;
  }
  
  /**
   * Sets layout and returns this appender
   *
   * @param   util.log.Layout layout
   * @return  util.log.Appender
   */
  public function withLayout(Layout $layout) {
    $this->layout= $layout;
    return $this;
  }

  /**
   * Gets layout
   *
   * @return  util.log.Layout
   */
  public function getLayout() {
    return $this->layout;
  }

  /**
   * Append data
   *
   * @param   util.log.LoggingEvent event
   */ 
  public abstract function append(LoggingEvent $event);
  
  /**
   * Finalize this appender. This method is called when the logger
   * is shut down. Does nothing in this default implementation.
   *
   */   
  public function finalize() { }

  /** @return string */
  public function toString() {
    return nameof($this).'(layout= '.Objects::stringOf($this->layout).')';
  }

  /** @return string */
  public function hashCode() {
    return $this->__id ?: $this->__id= sprintf('A%08x', ++self::$id);
  }

  /**
   * Compares this appender to another value
   *
   * @param  var $value
   * @return int
   */
  public function compareTo($value) {
    return $value instanceof self ? strcmp($this->hashCode(), $value->hashCode()) : 1;
  }
}
