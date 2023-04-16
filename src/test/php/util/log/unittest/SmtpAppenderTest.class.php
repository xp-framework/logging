<?php namespace util\log\unittest;

use test\Assert;
use test\Test;
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

  #[Test]
  public function append_sync() {
    $fixture= $this->newFixture('test', $sync= true);
    $fixture->append($this->newEvent(LogLevel::WARN, 'Test'));
    Assert::equals([['test', '[warn] Test']], $fixture->sent);
  }

  #[Test]
  public function append_sync_two_messages() {
    $fixture= $this->newFixture('test', $sync= true);
    $fixture->append($this->newEvent(LogLevel::WARN, 'Test'));
    $fixture->append($this->newEvent(LogLevel::INFO, 'Just testing'));
    Assert::equals(
      [['test', '[warn] Test'], ['test', '[info] Just testing']],
      $fixture->sent
    );
  }

  #[Test]
  public function finalize_sync() {
    $fixture= $this->newFixture('test', $sync= true);
    $fixture->append($this->newEvent(LogLevel::WARN, 'Test'));
    $sent= $fixture->sent;
    $fixture->finalize();
    Assert::equals($sent, $fixture->sent);
  }

  #[Test]
  public function append_async() {
    $fixture= $this->newFixture('test', $sync= false);
    $fixture->append($this->newEvent(LogLevel::WARN, 'Test'));
    Assert::equals([], $fixture->sent);
  }

  #[Test]
  public function finalize_async_no_messages() {
    $fixture= $this->newFixture('test', $sync= false);
    $fixture->finalize();
    Assert::equals([], $fixture->sent);
  }

  #[Test]
  public function finalize_async() {
    $fixture= $this->newFixture('test', $sync= false);
    $fixture->append($this->newEvent(LogLevel::WARN, 'Test'));
    $fixture->finalize();
    Assert::equals(
      [['test [1 entries]', "[warn] Test\n"]],
      $fixture->sent
    );
  }

  #[Test]
  public function finalize_async_two_messages() {
    $fixture= $this->newFixture('test', $sync= false);
    $fixture->append($this->newEvent(LogLevel::WARN, 'Test'));
    $fixture->append($this->newEvent(LogLevel::INFO, 'Just testing'));
    $fixture->finalize();
    Assert::equals(
      [['test [2 entries]', "[warn] Test\n[info] Just testing\n"]],
      $fixture->sent
    );
  }
}