<?php namespace util\log;

use io\IOException;
use util\Date;

/**
 * Appender which appends data to syslog via UDP
 *
 * @see rfc5424
 *
 */
class SyslogUdpAppender extends Appender {

  /**
   * Value is from monologs UdpSocket
   */
  const DATAGRAM_MAX_LENGTH = 65023;

  /** @var int int */
  private $facility;

  /** @var resource */
  private $socket= null;

  /** @var string */
  private $identifier;

  /** @var string */
  private $ip;

  /** @var int */
  private $port;

  /**
   * SyslogUdpAppender constructor.
   *
   * @param string $ip
   * @param int $port default 514
   * @param string|null $identifier default null (current filename)
   * @param int $facility default LOG_USER
   */
  public function __construct($ip, $port= 514, $identifier= null, $facility= LOG_USER) {
    $this->ip= $ip;
    $this->port= $port;
    $this->facility= $facility;
    $this->identifier= $identifier;
  }

  /**
   * Append data
   *
   * @param LoggingEvent $event
   */
  public function append(LoggingEvent $event) {
    $header= $this->buildHeader($event);
    $this->sendUdpPackage($header.substr(
      $this->layout->format($event),
      0,
      self::DATAGRAM_MAX_LENGTH - strlen($header)
    ));
  }

  /**
   * Builds syslog package header
   *
   * @see rfc5424
   * @param LoggingEvent $event
   * @return string
   */
  private function buildHeader(LoggingEvent $event) {
    static $map= [
      LogLevel::INFO    => LOG_INFO,
      LogLevel::WARN    => LOG_WARNING,
      LogLevel::ERROR   => LOG_ERR,
      LogLevel::DEBUG   => LOG_DEBUG,
      LogLevel::NONE    => LOG_NOTICE
    ];

    $l= $event->getLevel();
    $priority = $this->facility + ($map[isset($map[$l]) ? $l : LogLevel::NONE]);

    if (!$hostname= getHostName()) {
      $hostname= '-';
    }

    if (!$pid= getmypid()) {
      $pid = '-';
    }

    return
      '<'.$priority.'>1 '.
      $this->getCurrentDate().' '.
      $hostname.' '.
      ($this->identifier ?: basename($_SERVER['PHP_SELF'])).' '.
      $pid.' - - ';
  }

  /**
   * Sends buffer via udp to syslog
   *
   * @param $buffer
   */
  protected function sendUdpPackage($buffer) {
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
  protected function getCurrentDate() {
    return date(\DateTime::RFC3339);
  }

  /**
   * Finalize this appender.
   * This method is called when the logger is shut down
   *
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