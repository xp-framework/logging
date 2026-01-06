<?php namespace util\log;

use lang\Value;

/**
 * Takes care of formatting log entries
 *
 * @test  util.log.unittest.LayoutTest
 */
abstract class Layout {

  /**
   * Creates a string representation of the given argument.
   *
   * @param  var $arg
   * @return string
   */
  protected function stringOf($arg, $indent= '') {
    if (null === $arg) {
      return 'null';
    } else if (false === $arg) {
      return 'false';
    } else if (true === $arg) {
      return 'true';
    } else if (is_string($arg)) {
      return '' === $indent ? $arg : '"'.$arg.'"';
    } else if ($arg instanceof Value) {
      return $arg->toString();
    } else if ([] === $arg) {
      return '[]';
    } else if (is_array($arg)) {
      if (0 === key($arg)) {
        $r= '';
        foreach ($arg as $value) {
          $r.= ', '.$this->stringOf($value, $indent);
        }
        return '['.substr($r, 2).']';
      } else {
        $n= $indent.'  ';
        $r= '[';
        foreach ($arg as $key => $value) {
          $r.= "\n{$n}{$key} => ".$this->stringOf($value, $n);
        }
        return $r."\n{$indent}]";
      }
    } else if ($arg instanceof \Closure) {
      return $arg();
    } else if (is_object($arg)) {
      $n= $indent.'  ';
      $r= nameof($arg).'@{';
      foreach ((array)$arg as $key => $value) {
        $r.= "\n{$n}{$key} => ".$this->stringOf($value, $n);
      }
      return $r."\n{$indent}}";
    } else {
      return (string)$arg;
    }
  }

  /**
   * Formats a logging event according to this layout
   *
   * @param   util.log.LoggingEvent event
   * @return  string
   */
  public abstract function format(LoggingEvent $event);
}