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
      private $r, $k= -1, $c= null;
      public static function on($r) { $self= new self(); $self->r= $r; return $self; }
      public function current() { return $this->c; }
      public function key() { return $this->k; }
      public function next() { $this->c= $this->r->next(); $this->k++; }
      public function rewind() { if ($this->k > -1) throw new \lang\IllegalStateException("Cannot rewind iterator"); $this->i= 0; }
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