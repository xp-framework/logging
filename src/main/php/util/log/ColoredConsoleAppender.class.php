<?php namespace util\log;

/**
 * ConsoleAppender which colorizes output depending on the
 * logger flag (error, warn, info or debug).
 *
 * Uses the terminal emulation escape sequences to set colors.
 *
 * @see  http://www.catalyst.com/support/help/cstools3/visual/terminal/escapeseq.html
 * @see  http://www.termsys.demon.co.uk/vtansi.htm#colors  
 * @see  xp://util.log.ConsoleAppender
 * @test xp://util.log.unittest.ColoredConsoleAppenderTest
 */  
class ColoredConsoleAppender extends ConsoleAppender {
  private static $DEFAULTS= [
    LogLevel::INFO  => '00;00',
    LogLevel::WARN  => '00;31',
    LogLevel::ERROR => '01;31',
    LogLevel::DEBUG => '00;34'
  ];
  protected $colors= [];

  /**
   * Constructor
   *
   * @param  string|io.streams.StringWriter $target
   * @param  [:string] $colors
   * @throws lang.IllegalArgumentException
   */
  public function __construct($target= 'out', $colors= []) {
    parent::__construct($target);
    $this->colors= array_replace(self::$DEFAULTS, $colors);
  }

  /** @return [:string] */
  public function colors() { return $this->colors; }
  
  /**
   * Append data
   *
   * @param   util.log.LoggingEvent event
   */ 
  public function append(LoggingEvent $event) {
    $l= $event->getLevel();
    $this->writer->write(
      "\x1b[".($this->colors[$l] ?? '07;37')."m".
      $this->layout->format($event).
      "\x1b[0m"
    );
  }
}