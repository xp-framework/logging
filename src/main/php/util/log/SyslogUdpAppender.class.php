<?php namespace util\log;

/**
 * Appender which appends data to syslog via UDP
 *
 * @see rfc5424
 */
class SyslogUdpAppender extends Appender {
  const DATAGRAM_MAX_LENGTH = 65023;

  /** @var int */
  public $facility;

  /** @var resource */
  private $socket= null;

  /** @var string */
  public $identifier;

  /** @var string */
  public $ip;

  /** @var int */
  public $port;

  /** @var string */
  public $hostname;

  /**
   * SyslogUdpAppender constructor.
   *
   * @param string $ip default 127.0.0.1
   * @param int $port default 514
   * @param ?string $identifier - if omitted, uses PHP_SELF
   * @param int $facility default LOG_USER
   * @param ?string $hostname defaults to gethostname()
   */
  public function __construct($ip= '127.0.0.1', $port= 514, $identifier= null, $facility= LOG_USER, $hostname= null) {
    $this->ip= $ip;
    $this->port= $port;
    $this->identifier= $identifier ?: basename($_SERVER['PHP_SELF']);
    $this->facility= $facility;
    $this->hostname= $hostname ?: gethostname() ?: '-';
  }

  /**
   * Append data
   *
   * @param  util.log.LoggingEvent $event
   * @return void
   */
  public function append(LoggingEvent $event) {
    $header= $this->header($event);
    $this->send($header.substr($this->layout->format($event), 0, self::DATAGRAM_MAX_LENGTH - strlen($header)));
  }

  /**
   * Builds syslog package header
   *
   * @param  util.log.LoggingEvent $event
   * @return string
   */
  private function header($event) {
    static $map= [
      LogLevel::INFO  => LOG_INFO,
      LogLevel::WARN  => LOG_WARNING,
      LogLevel::ERROR => LOG_ERR,
      LogLevel::DEBUG => LOG_DEBUG,
    ];

    $l= $event->getLevel();
    $priority= $this->facility + ($map[$l] ?? LOG_NOTICE);
    return sprintf(
      '<%d>1 %s %s %s %s - - ',
      $priority,
      $this->currentDate(),
      $this->hostname,
      $this->identifier,
      (getmypid() ?: '-')
    );
  }

  /**
   * Sends buffer via udp to syslog
   *
   * @param  string $buffer
   * @return void
   */
  protected function send($buffer) {
    if ($this->socket === null) {
      $this->socket= socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
    }
    socket_sendto($this->socket, $buffer, strlen($buffer), 0, $this->ip, $this->port);
  }

  /**
   * Returns current date in RFC3339 format
   *
   * @return string
   */
  protected function currentDate() {
    return date(\DateTime::RFC3339);
  }

  /**
   * Finalize this appender. This method is called when the logger is shut down
   *
   * @return void
   */
  public function finalize() {
    if (is_resource($this->socket)) {
      socket_close($this->socket);
      $this->socket= null;
    }
  }

  /**
   * Destructor
   */
  public function __destruct() {
    $this->finalize();
  }
}