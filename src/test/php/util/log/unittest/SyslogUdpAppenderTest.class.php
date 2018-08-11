<?php namespace util\log\unittest;

use io\streams\MemoryInputStream;
use unittest\TestCase;
use util\Properties;
use util\log\LogLevel;
use util\log\Logger;
use util\log\LoggingEvent;
use util\log\SyslogUdpAppender;
use util\log\layout\PatternLayout;

class SyslogUdpAppenderTest extends TestCase {

  /**
   * Creates new Syslog UDP appender fixture
   *
   * @param string $ip
   * @param int $port
   * @param null $identifier
   * @param int $facility
   * @return SyslogUdpAppender
   * @throws \lang\IllegalArgumentException
   */
  protected function newFixture(
    $identifier= null,
    $mockDate= null
  ) {
    $appender= newinstance(SyslogUdpAppender::class,
      ['127.0.0.1', 514, $identifier, LOG_USER],
      '
        {
          public $lastBuffer;
        
          public function sendUdpPackage($buffer) {
            $this->lastBuffer= $buffer;
          }
          
          '.($mockDate ? 'protected function getCurrentDate() {
            return \''.$mockDate.'\';
          }' : '') .'
          
        }
      '
    );
    return $appender->withLayout(new PatternLayout('%m'));
  }

  #[@test]
  public function test_sentData() {
    $testMessage= 'BOM\'su root\' failed for lonvick on /dev/pts/8';

    // Identifier set
    $appender= $this->newFixture(
      'su',
      '2003-10-11T22:14:15.003Z'
    );
    $appender->append((new LoggingEvent(
      'testCat', time(), 1234, LogLevel::ERROR, [$testMessage]
    )));
    $this->assertEquals(
      '<'.(LOG_USER + LOG_ERR).'>1 2003-10-11T22:14:15.003Z '.gethostname().' su '.getmypid().' - - '.$testMessage,
      $appender->lastBuffer
    );

    // Identifier generated from filename
    $appender= $this->newFixture(
      null,
      '2003-10-11T22:14:15.003Z'
    );
    $appender->append((new LoggingEvent(
      'testCat', time(), 1234, LogLevel::ERROR, ['BOM\'su root\' failed for lonvick on /dev/pts/8']
    )));
    $this->assertEquals(
      '<'.(LOG_USER + LOG_ERR).'>1 2003-10-11T22:14:15.003Z '.gethostname().' '. basename($_SERVER['PHP_SELF']).' '.getmypid().' - - '.$testMessage,
      $appender->lastBuffer
    );
  }

  #[@test]
  public function test_sendedData_maxLangth() {
    $appender= $this->newFixture();
    $appender->append((new LoggingEvent(
      'testCat', time(), 1234, LogLevel::ERROR, [str_pad('', 70000, '0')]
    )));
    $this->assertEquals(SyslogUdpAppender::DATAGRAM_MAX_LENGTH, strlen($appender->lastBuffer));
  }

}