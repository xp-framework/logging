<?php namespace util\log\unittest;

class MemoryMapped {
  public static $buffer= [];
  private static $meta= [STREAM_META_ACCESS => 0666];
  private $handle;
  public $context;

  public function stream_open($path, $mode, $options, $opened_path) {
    if (strstr($mode, 'r')) {
      if (!isset(self::$buffer[$path])) return false;
      self::$buffer[$path][0]= $mode;
      self::$buffer[$path][1]= 0;
    } else if (strstr($mode, 'w')) {
      self::$buffer[$path]= [$mode, 0, '', self::$meta];
    } else if (strstr($mode, 'a')) {
      if (!isset(self::$buffer[$path])) {
        self::$buffer[$path]= [$mode, 0, '', self::$meta];
      } else {
        self::$buffer[$path][0]= $mode;
      }
    }
    $this->handle= &self::$buffer[$path];
    return true;
  }

  public function stream_write($data) {
    $this->handle[1]+= strlen($data);
    $this->handle[2].= $data;
    return strlen($data);
  }

  public function stream_read($count) {
    $chunk= substr($this->handle[2], $this->handle[1], $count);
    $this->handle[1]+= strlen($chunk);
    $this->handle[2]= substr($this->handle[2], $this->handle[1]);
    return $chunk;
  }

  public function stream_flush() {
    return true;
  }

  public function stream_seek($offset, $whence) {
    if (SEEK_SET === $whence) {
      $this->handle[1]= $offset;
    } else if (SEEK_END === $whence) {
      $this->handle[1]= strlen($this->handle[2]);
    } else if (SEEK_CUR === $whence) {
      $this->handle[1]+= $offset;
    }
    return 0;   // Success
  }

  public function stream_eof() {
    return $this->handle[1] >= strlen($this->handle[2]);
  }

  public function stream_stat() {
    return ['size' => $this->handle[1]];
  }

  public function stream_close() {
    return true;
  }

  public function stream_metadata($path, $option, $value) {
    if (!isset(self::$buffer[$path])) return false;
    self::$buffer[$path][3][$option]= $value;
    return true;
  }

  public function url_stat($path) {
    if (!isset(self::$buffer[$path])) return false;
    return [
      'size' => strlen(self::$buffer[$path][2]),
      'mode' => self::$buffer[$path][3][STREAM_META_ACCESS]
    ];
  }
}