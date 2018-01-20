<?php namespace util\log\unittest;

use util\log\FileAppender;
use io\streams\Streams;
use io\streams\MemoryOutputStream;
use util\log\layout\PatternLayout;
use util\log\LogLevel;

/**
 * TestCase for FileAppender
 *
 * @see   xp://util.log.FileAppender
 */
class FileAppenderTest extends AppenderTest {
  private $tz;

  /**
   * Defines stream wrapper
   */
  #[@beforeClass]
  public static function registerStreamWrapper() {
    stream_wrapper_register('mem', MemoryMapped::class);
  }

  /**
   * Sets up test case.
   *
   * @return void
   */
  public function setUp() {
    $this->tz= date_default_timezone_get();
    date_default_timezone_set('Europe/Berlin');
  }

  /**
   * Tears down test
   *
   * @return void
   */
  public function tearDown() {
    date_default_timezone_set($this->tz);
  }

  /**
   * Creates new appender fixture
   *
   * @return  util.log.BufferedAppender
   */
  protected function newFixture() {
    return (new FileAppender('mem://'.$this->name))->withLayout(new PatternLayout("[%l] %m\n"));
  }

  #[@test]
  public function append_one_message() {
    $fixture= $this->newFixture();
    $fixture->append($this->newEvent(LogLevel::WARN, 'Test'));
    $this->assertEquals(
      "[warn] Test\n",
      file_get_contents($fixture->filename)
    );
  }

  #[@test]
  public function append_two_messages() {
    $fixture= $this->newFixture();
    $fixture->append($this->newEvent(LogLevel::WARN, 'Test'));
    $fixture->append($this->newEvent(LogLevel::INFO, 'Just testing'));
    $this->assertEquals(
      "[warn] Test\n[info] Just testing\n",
      file_get_contents($fixture->filename)
    );
  }

  #[@test]
  public function chmod_called_when_perms_given() {
    $fixture= $this->newFixture();
    $fixture->perms= '0640';  // -rw-r-----
    $fixture->append($this->newEvent(LogLevel::WARN, 'Test'));
    $this->assertEquals(0640, fileperms($fixture->filename));
  }

  #[@test]
  public function chmod_not_called_without_initializing_perms() {
    $fixture= $this->newFixture();
    $fixture->append($this->newEvent(LogLevel::WARN, 'Test'));
    $this->assertEquals(0666, fileperms($fixture->filename));
  }

  #[@test]
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

    $this->assertEquals(
      ['fn1' => true, 'fn2' => true, 'fn3' => false],
      ['fn1' => file_exists('mem://fn01'), 'fn2' => file_exists('mem://fn02'), 'fn3' => file_exists('mem://fn03')]
    );
  }

  #[@test]
  public function filename_does_not_sync_with_time() {
    $fixture= $this->newFixture();
    $fixture->filename= 'mem://file-%H:%M:%I:%S';
    $fixture->syncDate= false;

    $fixture->filename();
    $this->assertFalse(strpos($fixture->filename, '%'));
  }
}
