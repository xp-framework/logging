<?php namespace util\log\unittest;

use io\streams\{MemoryOutputStream, Streams};
use test\{After, Assert, Before, Test};
use util\log\layout\PatternLayout;
use util\log\{FileAppender, LogLevel};

class FileAppenderTest extends AppenderTest {
  private $tz;

  #[Before]
  public function setUp() {
    stream_wrapper_register('mem', MemoryMapped::class);

    // Save timezone, it will be restored inside tearDown()
    $this->tz= date_default_timezone_get();
    date_default_timezone_set('Europe/Berlin');
  }

  #[After]
  public function tearDown() {
    stream_wrapper_unregister('mem');
    date_default_timezone_set($this->tz);
  }

  /** @return  util.log.BufferedAppender */
  private function newFixture() {
    return (new FileAppender('mem://'.$this->name))->withLayout(new PatternLayout("[%l] %m\n"));
  }

  #[Test]
  public function append_one_message() {
    $fixture= $this->newFixture();
    $fixture->append($this->newEvent(LogLevel::WARN, 'Test'));
    Assert::equals(
      "[warn] Test\n",
      file_get_contents($fixture->filename)
    );
  }

  #[Test]
  public function append_two_messages() {
    $fixture= $this->newFixture();
    $fixture->append($this->newEvent(LogLevel::WARN, 'Test'));
    $fixture->append($this->newEvent(LogLevel::INFO, 'Just testing'));
    Assert::equals(
      "[warn] Test\n[info] Just testing\n",
      file_get_contents($fixture->filename)
    );
  }

  #[Test]
  public function chmod_called_when_perms_given() {
    $fixture= $this->newFixture();
    $fixture->perms= '0640';  // -rw-r-----
    $fixture->append($this->newEvent(LogLevel::WARN, 'Test'));
    Assert::equals(0640, fileperms($fixture->filename));
  }

  #[Test]
  public function chmod_not_called_without_initializing_perms() {
    $fixture= $this->newFixture();
    $perms= fileperms($fixture->filename);
    $fixture->append($this->newEvent(LogLevel::WARN, 'Test'));
    Assert::equals($perms, fileperms($fixture->filename));
  }

  #[Test]
  public function filename_syncs_with_time() {
    $fixture= newinstance(FileAppender::class, ['mem://fn%H'], '{
      protected $hour= 0;
      public function filename($ref= null) {
        return parent::filename(0 + 3600 * $this->hour++);
      }
    }');
    $fixture->setLayout(new PatternLayout("[%l] %m\n"));

    $fixture->append($this->newEvent(LogLevel::INFO, 'One'));
    $fixture->append($this->newEvent(LogLevel::INFO, 'Two'));

    Assert::equals(
      ['fn1' => true, 'fn2' => true, 'fn3' => false],
      ['fn1' => file_exists('mem://fn01'), 'fn2' => file_exists('mem://fn02'), 'fn3' => file_exists('mem://fn03')]
    );
  }

  #[Test]
  public function filename_does_not_sync_with_time() {
    $fixture= $this->newFixture();
    $fixture->filename= 'mem://file-%H:%M:%I:%S';
    $fixture->syncDate= false;

    $fixture->filename();
    Assert::false(strpos($fixture->filename, '%'));
  }
}