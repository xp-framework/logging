<?php namespace util\log;

use util\log\layout\DefaultLayout;
use lang\Value;
use util\Objects;

/**
 * The log category is the interface to be used. All logging information
 * is sent to a log category via one of the info, warn, error, debug 
 * methods which accept any number of arguments of any type (or 
 * their *f variants which use sprintf).
 *
 * Basic example:
 * <code>
 *   $cat= Logger::getInstance()->getCategory();
 *   $cat->addAppender(new ConsoleAppender());
 *
 *   // ...
 *   $cat->info('Starting work at', Date::now());
 *
 *   // ...
 *   $cat->debugf('Processing %d rows took %.3f seconds', $rows, $delta);
 *
 *   try {
 *     // ...
 *   } catch (SocketException $e) {
 *     $cat->warn('Caught', $e);
 *   }
 * </code>
 *
 * @test     xp://net.xp_framework.unittest.logging.LogCategoryTest
 */
class LogCategory implements Value {
  protected static $DEFAULT_LAYOUT= null;

  protected $_appenders= [];
  protected $context= null;
  public $flags= 0;
  public $identifier= '';
    
  static function __static() {
    self::$DEFAULT_LAYOUT= new DefaultLayout();
  }

  /**
   * Constructor
   *
   * @param   string identifier
   * @param   int flags (defaults to all)
   * @param   util.log.Context context
   */
  public function __construct($identifier= 'default', $flags= LogLevel::ALL, $context= null) {
    $this->flags= $flags;
    $this->identifier= $identifier;
    $this->context= $context;
    $this->_appenders= [];
  }

  /**
   * Setter for context
   *
   * @param  util.log.Context context
   * @return void
   */
  public function setContext(Context $context) {
  	$this->context= $context;
  }
  
  /**
   * Retrieves whether this log category has a context
   *
   * @return bool
   */
  public function hasContext() {
  	return null !== $this->context;
  }
  
  /**
   * Getter for context
   *
   * @return util.log.Context
   */
  public function getContext() {
  	return $this->context;
  }

  /**
   * Sets the flags (what should be logged). Note that you also
   * need to add an appender for a category you want to log.
   *
   * @param   int flags bitfield with flags (LogLevel::*)
   */
  public function setFlags($flags) {
    $this->flags= $flags;
  }
  
  /**
   * Gets flags
   *
   * @return  int flags
   */
  public function getFlags() {
    return $this->flags;
  }
  
  /**
   * Calls all appenders
   *
   * @param   int level
   * @param   var[] args
   */
  public function log($level, $args) {
    if (!($this->flags & $level)) return;
    $event= new LoggingEvent($this, time(), getmypid(), $level, $args);
    foreach ($this->_appenders as $appflag => $appenders) {
      if (!($level & $appflag)) continue;
      foreach ($appenders as $appender) {
        $appender->append($event);
      }
    }
  }

  /**
   * Retrieves whether this log category has appenders
   *
   * @return  bool
   */
  public function hasAppenders() {
    return !empty($this->_appenders);
  }
  
  /**
   * Finalize
   *
   * @return void
   */
  public function finalize() {
    foreach ($this->_appenders as $flags => $appenders) {
      foreach ($this->_appenders[$appflag] as $appender) {
        $appender->finalize();
      }
    }
  }
  
  /**
   * Adds an appender for the given log categories. Use logical OR to 
   * combine the log types or use LogLevel::ALL (default) to log all 
   * types.
   *
   * @param   util.log.Appender appender The appender object
   * @param   int flag default LogLevel::ALL
   * @return  util.log.Appender the appender added
   */
  public function addAppender(Appender $appender, $flag= LogLevel::ALL) {
    $appender->getLayout() || $appender->setLayout(self::$DEFAULT_LAYOUT);
    $this->_appenders[$flag][$appender->hashCode()]= $appender;
    return $appender;
  }

  /**
   * Adds an appender for the given log categories and returns this
   * category - for use in a fluent interface way. Use logical OR to 
   * combine the log types or use LogLevel::ALL (default) to log all 
   * types.
   *
   * @param   util.log.Appender appender The appender object
   * @param   int flag default LogLevel::ALL
   * @return  util.log.LogCategory this category
   */
  public function withAppender(Appender $appender, $flag= LogLevel::ALL) {
    $appender->getLayout() || $appender->setLayout(self::$DEFAULT_LAYOUT);
    $this->_appenders[$flag][$appender->hashCode()]= $appender;
    return $this;
  }
  
  /**
   * Remove the specified appender from the given log categories. For usage
   * of log category flags, see addAppender().
   * 
   * @param   util.log.Appender appender
   * @param   int flag default LogLevel::ALL
   */
  public function removeAppender(Appender $appender, $flag= LogLevel::ALL) {
    foreach ($this->_appenders as $f => $appenders) {
      if (!($f & $flag)) continue;
      unset($this->_appenders[$f][$appender->hashCode()]);
      
      // Last appender for this flag removed - remove flag alltogether
      if (0 === sizeof($this->_appenders[$f])) {
        unset($this->_appenders[$f]);
      }
    }
  }
  
  /**
   * Gets appenders
   *
   * @param   int flag
   * @return  util.log.Appender[]
   */
  public function getAppenders($flag= LogLevel::ALL) {
    $r= [];
    foreach ($this->_appenders as $f => $appenders) {
      if ($f & $flag) $r= array_merge($r, array_values($appenders));
    }
    return $r;
  }

  /**
   * Appends a log of type info. Accepts any number of arguments of
   * any type. 
   *
   * The common rule (though up to each appender on how to realize it)
   * for serialization of an argument is:
   *
   * <ul>
   *   <li>For XP objects, the toString() method will be called
   *       to retrieve its representation</li>
   *   <li>Strings are printed directly</li>
   *   <li>Any other type is serialized using var_export()</li>
   * </ul>
   *
   * Note: This also applies to warn(), error() and debug().
   *
   * @param  var... $args
   */
  public function info(... $args) {
    $this->log(LogLevel::INFO, $args);
  }

  /**
   * Appends a log of type info in sprintf-style. The first argument
   * to this method is the format string, containing sprintf-tokens,
   * the rest of the arguments are used as argument to sprintf. 
   *
   * Note: This also applies to warnf(), errorf() and debugf().
   *
   * @param  string $format 
   * @param  var... $args
   */
  public function infof($format, ... $args) {
    $this->log(LogLevel::INFO, [vsprintf($format, $args)]);
  }

  /**
   * Appends a log of type warn
   *
   * @param  var... $args
   */
  public function warn(... $args) {
    $this->log(LogLevel::WARN, $args);
  }

  /**
   * Appends a log of type info in printf-style
   *
   * @param  string $format 
   * @param  var... $args
   */
  public function warnf($format, ... $args) {
    $this->log(LogLevel::WARN, [vsprintf($format, $args)]);
  }

  /**
   * Appends a log of type error
   *
   * @param  var... $args
   */
  public function error(... $args) {
    $this->log(LogLevel::ERROR, $args);
  }

  /**
   * Appends a log of type info in printf-style
   *
   * @param  string $format 
   * @param  var... $args
   */
  public function errorf($format, ... $args) {
    $this->log(LogLevel::ERROR, [vsprintf($format, $args)]);
  }

  /**
   * Appends a log of type debug
   *
   * @param  var... $args
   */
  public function debug(... $args) {
    $this->log(LogLevel::DEBUG, $args);
  }
 
  /**
   * Appends a log of type info in printf-style
   *
   * @param  string $format format string
   * @param  var... $args
   */
  public function debugf($format, ... $args) {
    $this->log(LogLevel::DEBUG, [vsprintf($format, $args)]);
  }
 
  /**
   * Appends a separator (a "line" consisting of 72 dashes)
   *
   */
  public function mark() {
    $this->log(LogLevel::INFO, [str_repeat('-', 72)]);
  }

  /** @return string */
  public function toString() {
    $s= nameof($this).'(name='.$this->identifier.' flags='.$this->flags.")@{\n";
    foreach ($this->_appenders as $flags => $appenders) {
      $s.= '  '.$flags.": [\n";
      foreach ($appenders as $appender) {
        $s.= '  - '.$appender->toString()."\n"; 
      }
      $s.= "  ]\n";
    }
    return $s.'}';
  }

  /** @return string */
  public function hashCode() {
    return Objects::hashOf([$this->identifier, $this->flags, $this->context, $this->_appenders]);
  }

  /**
   * Compares this appender to another value
   *
   * @param  var $value
   * @return int
   */
  public function compareTo($value) {
    return $value instanceof self
      ? Objects::compare(
          [$this->identifier, $this->flags, $this->context, $this->_appenders],
          [$value->identifier, $value->flags, $value->context, $value->_appenders]
        )
      : 1
    ;
  }
}
