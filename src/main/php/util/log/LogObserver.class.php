<?php namespace util\log;

/**
 * Observer interface
 *
 * @test  xp://util.log.unittest.LogObserverTest
 * @see   xp://util.Observable
 */
class LogObserver implements BoundLogObserver {
  public $cat= null;
  
  /**
   * Creates a new log observer with a given log category.
   *
   * @param   util.log.LogCategory cat
   */
  public function __construct(LogCategory $cat) {
    $this->cat= $cat;
  }

  /**
   * Retrieve instance bound to log category.
   *
   * @param   string arg
   * @return  util.log.LogObserver
   */
  public static function instanceFor($arg) {
    static $inst= [];
    
    if (!isset($inst[$arg])) {
      $inst[$arg]= new self(Logger::getInstance()->getCategory($arg));
    }
    
    return $inst[$arg];
  }

  /**
   * Update method
   *
   * @param   util.Observable obs
   * @param   var arg default NULL
   */
  public function update($obs, $arg= null) {
    $this->cat->debug($arg);
  }
} 