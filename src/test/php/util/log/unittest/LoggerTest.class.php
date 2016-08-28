<?php namespace util\log\unittest;
 
use util\log\LogCategory;
use util\Configurable;
use util\log\FileAppender;
use util\log\SmtpAppender;
use util\log\context\NestedLogContext;
use util\log\Logger;
use util\Properties;

/**
 * Tests Logger class
 */
class LoggerTest extends \unittest\TestCase {
  protected $logger= null;
  
  /** @return void */
  public function setUp() {
    $this->logger= Logger::getInstance();
  }

  /** @return void */
  public function tearDown() {
    $this->logger->finalize();
  }

  /**
   * Configures logger
   *
   * @param  string $properties
   * @return void
   */
  private function configure($properties) {
    $p= new Properties(null);
    $p->load(trim($properties));
    $this->logger->configure($p);
  }
  
  #[@test]
  public function loggerIsASingleton() {
    $this->assertTrue($this->logger === Logger::getInstance());
  }

  #[@test]
  public function defaultCategory() {
    with ($cat= $this->logger->getCategory()); {
      $this->assertInstanceOf(LogCategory::class, $cat);
      $this->assertFalse($cat->hasAppenders());
    }
  }

  #[@test]
  public function isConfigurable() {
    $this->assertInstanceOf(Configurable::class, $this->logger);
  }

  #[@test]
  public function configureMultipleCategories() {
    $this->configure('
[sql]
appenders="util.log.FileAppender"
appender.util.log.FileAppender.params="filename"
appender.util.log.FileAppender.param.filename="/var/log/xp/sql.log"

[remote]
appenders="util.log.FileAppender"
appender.util.log.FileAppender.params="filename"
appender.util.log.FileAppender.param.filename="/var/log/xp/remote.log"
    ');
    
    with ($sql= $this->logger->getCategory('sql')); {
      $appenders= $sql->getAppenders();
      $this->assertInstanceOf(FileAppender::class, $appenders[0]);
      $this->assertEquals('/var/log/xp/sql.log', $appenders[0]->filename);
    }
    
    with ($sql= $this->logger->getCategory('remote')); {
      $appenders= $sql->getAppenders();
      $this->assertInstanceOf(FileAppender::class, $appenders[0]);
      $this->assertEquals('/var/log/xp/remote.log', $appenders[0]->filename);
    }
  }

  #[@test]
  public function configureMultipleAppenders() {
    $this->configure('
[sql]
appenders="util.log.FileAppender|util.log.SmtpAppender"
appender.util.log.FileAppender.params="filename"
appender.util.log.FileAppender.param.filename="/var/log/xp/sql.log"
appender.util.log.SmtpAppender.params="email"
appender.util.log.SmtpAppender.param.email="xp@example.com"
    ');

    with ($sql= $this->logger->getCategory('sql')); {
      $appenders= $sql->getAppenders();
      $this->assertInstanceOf(FileAppender::class, $appenders[0]);
      $this->assertEquals('/var/log/xp/sql.log', $appenders[0]->filename);
      $this->assertInstanceOf(SmtpAppender::class, $appenders[1]);
      $this->assertEquals('xp@example.com', $appenders[1]->email);
    }
  }

  #[@test]
  public function configureWithFlags() {
    $this->configure('
[sql]
appenders="util.log.FileAppender"
appender.util.log.FileAppender.params="filename"
appender.util.log.FileAppender.param.filename="/var/log/xp/sql-errors_%Y-%m-%d.log"
appender.util.log.FileAppender.flags="LOGGER_FLAG_ERROR|LOGGER_FLAG_WARN"
    ');

    with ($cat= $this->logger->getCategory('sql')); {
      $this->assertFalse($cat === $this->logger->getCategory());
      $this->assertInstanceOf(LogCategory::class, $cat);
      $this->assertTrue($cat->hasAppenders());
      with ($appenders= $cat->getAppenders(\util\log\LogLevel::ERROR | \util\log\LogLevel::WARN)); {
        $this->assertInstanceOf(FileAppender::class, $appenders[0]);
      }
    }
  }

  #[@test]
  public function configureWithLevels() {
    $this->configure('
[sql]
appenders="util.log.FileAppender"
appender.util.log.FileAppender.params="filename"
appender.util.log.FileAppender.param.filename="/var/log/xp/sql-errors_%Y-%m-%d.log"
appender.util.log.FileAppender.levels="ERROR|WARN"
    ');

    with ($cat= $this->logger->getCategory('sql')); {
      $this->assertFalse($cat === $this->logger->getCategory());
      $this->assertInstanceOf(LogCategory::class, $cat);
      $this->assertTrue($cat->hasAppenders());
      with ($appenders= $cat->getAppenders(\util\log\LogLevel::ERROR | \util\log\LogLevel::WARN)); {
        $this->assertInstanceOf(FileAppender::class, $appenders[0]);
      }
    }
  }

  #[@test]
  public function configureWithContext() {
    $this->configure('
[context]
appenders="util.log.FileAppender"
context="util.log.context.NestedLogContext"
appender.util.log.FileAppender.params="filename"
appender.util.log.FileAppender.param.filename="/var/log/xp/default.log"
    ');

    with ($cat= $this->logger->getCategory('context')); {
      $this->assertTrue($cat->hasContext());
      $this->assertInstanceOf(NestedLogContext::class, $cat->getContext());
    }
  }

  #[@test]
  public function configureWithLayout() {
    $this->configure('
[fixture]
appenders="util.log.FileAppender"
appender.util.log.FileAppender.params="filename"
appender.util.log.FileAppender.param.filename="/var/log/xp/default.log"
appender.util.log.FileAppender.layout="util.log.layout.PatternLayout|%m"
    ');

    $this->assertInstanceOf(
      'util.log.layout.PatternLayout',
      $this->logger->getCategory('fixture')->getAppenders()[0]->getLayout()
    );
  }
}
