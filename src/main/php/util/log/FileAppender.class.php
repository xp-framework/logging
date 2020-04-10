<?php namespace util\log;

use io\File;

/**
 * Appender which appends data to a file
 *
 * Supported parameters:
 * <ul><li>string filename - the file name to log to; may contain strftime() token which
 *     will be automatically replaced
 * </li><li>bool syncDate - whether to recalculate the log file name for every line written.
 *     Set this to FALSE to calculcate it only once.
 * </li><li>int perms; file permissions
 * </li></ul>
 *
 * @see   xp://util.log.Appender
 * @test  xp://net.xp_framework.unittest.logging.FileAppenderTest
 */  
class FileAppender extends Appender {
  public 
    $filename = '',
    $perms    = null,
    $syncDate = true;
  
  /**
   * Constructor
   *
   * @param  string|io.Path|io.File $file
   */
  public function __construct($file= 'php://stderr') {
    if ($file instanceof File) {
      $this->filename= $file->getURI();
    } else {
      $this->filename= (string)$file;
    }
  }

  /**
   * Retrieve current log file name
   *
   * @return string
   */
  public function filename($ref= null) {
    $formatted= null === $ref ? strftime($this->filename) : strftime($this->filename, $ref);
    if (!$this->syncDate) {
      $this->filename= $formatted;
    }
    return $formatted;
  }
  
  /**
   * Append data
   *
   * @param   util.log.LoggingEvent $event
   */ 
  public function append(LoggingEvent $event) {
    $fn= $this->filename();
    file_put_contents($fn, $this->layout->format($event), FILE_APPEND);
    $this->perms && chmod($fn, octdec($this->perms));
  }
}