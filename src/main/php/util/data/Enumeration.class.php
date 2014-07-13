<?php namespace util\data;

use util\XPIterator;
use lang\IllegalArgumentException;

/**
 * Enumeration support for iterables, iterators, generators and arrays.
 *
 * ```php
 * foreach (Enumeration::of($arg) as $value) {
 *   // TBI
 * }
 * ```
 *
 * @see   php://class.traversable
 * @see   php://language.oop5.iterations
 * @see   php://generators
 * @see   xp://util.XPIterator
 * @test  xp://util.data.unittest.EnumerationTest
 */
abstract class Enumeration extends \lang\Object {
  protected static $iterate;

  static function __static() {
    self::$iterate= newinstance('Iterator', [], '{
      private $i, $r;
      public static function on($r) { $self= new self(); $self->r= $r; $self->i= 0; return $self; }
      public function current() { return $this->r->next(); }
      public function key() { return $this->i; }
      public function next() { $this->i++; }
      public function rewind() { /* NOOP */ }
      public function valid() { return $this->r->hasNext(); }
    }');
  }

  /**
   * Verifies a given argument is an enumeration
   *
   * @param  var $arg
   * @return var
   * @throws lang.IllegalArgumentException
   */
  public static function of($arg) {
    if ($arg instanceof \Traversable) {
      return $arg;
    } else if ($arg instanceof \Closure) {
      $generator= $arg();
      if ($generator instanceof \Generator) {
        return $generator;
      }
    } else if ($arg instanceof XPIterator) {
      return self::$iterate->on($arg);
    } else if (is_array($arg)) {
      return $arg;
    }

    throw new IllegalArgumentException('Expecting either an iterator, iterable, generator or an array');
  }
}