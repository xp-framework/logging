<?php namespace util\log\unittest;

use util\log\layout\PatternLayout;
use util\log\{LogLevel, SmtpAppender};

class SmtpAppenderTest extends AppenderTest {

  /**
   * Creates new SMTP appender fixture
   *
   * @param   string prefix
   * @param   bool sync
   * @return  util.log.SmtpAppender
   */
  protected function newFixture($prefix, $sync) {
    $appender= new class('test@example.com', $prefix, $sync) extends SmtpAppender {
      public $sent= [];
      protected function send($prefix, $content) {
        $this->sent[]= [$prefix, $content];
      }
    };
    return $appender->withLayout(new PatternLayout('[%l] %m'));
  }

  #[@test]
  public function append_sync() {
    $fixture= $this->newFixture('test', $sync= true);
    $fixture->append($this->newEvent(LogLevel::WARN, 'Test'));
    $this->assertEquals([['test', '[warn] Test']], $fixture->sent);
  }

  #[@test]
  public function append_sync_two_messages() {
    $fixture= $this->newFixture('test', $sync= true);
    $fixture->append($this->newEvent(LogLevel::WARN, 'Test'));
    $fixture->append($this->newEvent(LogLevel::INFO, 'Just testing'));
    $this->assertEquals(
      [['test', '[warn] Test'], ['test', '[info] Just testing']],
      $fixture->sent
    );
  }

  #[@test]
  public function finalize_sync() {
    $fixture= $this->newFixture('test', $sync= true);
    $fixture->append($this->newEvent(LogLevel::WARN, 'Test'));
    $sent= $fixture->sent;
    $fixture->finalize();
    $this->assertEquals($sent, $fixture->sent);
  }

  #[@test]
  public function append_async() {
    $fixture= $this->newFixture('test', $sync= false);
    $fixture->append($this->newEvent(LogLevel::WARN, 'Test'));
    $this->assertEquals([], $fixture->sent);
  }

  #[@test]
  public function finalize_async_no_messages() {
    $fixture= $this->newFixture('test', $sync= false);
    $fixture->finalize();
    $this->assertEquals([], $fixture->sent);
  }

  #[@test]
  public function finalize_async() {
    $fixture= $this->newFixture('test', $sync= false);
    $fixture->append($this->newEvent(LogLevel::WARN, 'Test'));
    $fixture->finalize();
    $this->assertEquals(
      [['test [1 entries]', "[warn] Test\n"]],
      $fixture->sent
    );
  }

  #[@test]
  public function finalize_async_two_messages() {
    $fixture= $this->newFixture('test', $sync= false);
    $fixture->append($this->newEvent(LogLevel::WARN, 'Test'));
    $fixture->append($this->newEvent(LogLevel::INFO, 'Just testing'));
    $fixture->finalize();
    $this->assertEquals(
      [['test [2 entries]', "[warn] Test\n[info] Just testing\n"]],
      $fixture->sent
    );
  }
}