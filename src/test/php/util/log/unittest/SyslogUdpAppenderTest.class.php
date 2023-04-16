<?php namespace util\log\unittest;

use test\Assert;
use test\{Test, TestCase, Values};
use util\log\layout\PatternLayout;
use util\log\{LogLevel, LoggingEvent, SyslogUdpAppender};

class SyslogUdpAppenderTest {

  /**
   * Creates new Syslog UDP appender fixture
   *
   * @param  string $identifier
   * @param  string $mockDate
   * @return util.log.SyslogUdpAppender
   */
  private function newFixture($identifier, $mockDate) {
    $appender= newinstance(SyslogUdpAppender::class, ['127.0.0.1', 514, $identifier, LOG_USER], [
      'lastBuffer'  => '',
      'send'        => function($buffer) { $this->lastBuffer= $buffer; },
      'currentDate' => function() use($mockDate) { return $mockDate; },
    ]);
    return $appender->withLayout(new PatternLayout('%m'));
  }

  /** @return iterable */
  private function levels() {
    yield [LogLevel::DEBUG, LOG_USER + LOG_DEBUG];
    yield [LogLevel::INFO,  LOG_USER + LOG_INFO];
    yield [LogLevel::WARN,  LOG_USER + LOG_WARNING];
    yield [LogLevel::ERROR, LOG_USER + LOG_ERR];
    yield [LogLevel::NONE,  LOG_USER + LOG_NOTICE];
  }

  #[Test]
  public function identifier_defaults_to_php_self() {
    $fixture= new SyslogUdpAppender('127.0.0.1', 514, null, LOG_USER);
    Assert::equals(basename($_SERVER['PHP_SELF']), $fixture->identifier);
  }

  #[Test]
  public function identifier_can_be_set() {
    $fixture= new SyslogUdpAppender('127.0.0.1', 514, 'test-identifier', LOG_USER);
    Assert::equals('test-identifier', $fixture->identifier);
  }

  #[Test]
  public function hostname_defaults_to_gethostname() {
    $fixture= new SyslogUdpAppender('127.0.0.1', 514, null, LOG_USER);
    Assert::equals(gethostname(), $fixture->hostname);
  }

  #[Test]
  public function hostname_can_be_set() {
    $fixture= new SyslogUdpAppender('127.0.0.1', 514, null, LOG_USER, 'test-host');
    Assert::equals('test-host', $fixture->hostname);
  }

  #[Test, Values(from: 'levels')]
  public function formatting($level, $priority) {
    $message= 'BOM\'su root\' failed for lonvick on /dev/pts/8';

    $appender= $this->newFixture('su', '2003-10-11T22:14:15.003Z');
    $appender->append(new LoggingEvent('testCat', time(), 1234, $level, [$message]));

    Assert::equals(
      '<'.$priority.'>1 2003-10-11T22:14:15.003Z '.gethostname().' su '.getmypid().' - - '.$message,
      $appender->lastBuffer
    );
  }

  #[Test]
  public function message_is_cut_when_maximum_length_is_reached() {
    $message= str_repeat('*', SyslogUdpAppender::DATAGRAM_MAX_LENGTH + 1);

    $appender= $this->newFixture('su', '2003-10-11T22:14:15.003Z');
    $appender->append(new LoggingEvent('testCat', time(), 1234, LogLevel::ERROR, [$message]));

    Assert::equals(SyslogUdpAppender::DATAGRAM_MAX_LENGTH, strlen($appender->lastBuffer));
  }
}