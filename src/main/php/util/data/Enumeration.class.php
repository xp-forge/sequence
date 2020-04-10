<?php namespace util\data;

use lang\IllegalArgumentException;
use util\XPIterator;

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
abstract class Enumeration {

  /**
   * Verifies a given argument is an enumeration
   *
   * @param  var $arg
   * @return iterable
   * @throws lang.IllegalArgumentException
   */
  public static function of($arg) {
    if ($arg instanceof Sequence) {
      return $arg;
    } else if ($arg instanceof \Generator) {
      return new YieldingOf($arg);
    } else if ($arg instanceof \Traversable) {
      return new TraversalOf($arg);
    } else if ($arg instanceof \Closure) {
      $result= $arg();
      return $result instanceof \Generator ? new YieldingOf($result) : self::of($result);
    } else if ($arg instanceof XPIterator) {
      return new XPIteratorAdapter($arg);
    } else if (null === $arg) {
      return [];
    } else if (is_array($arg)) {
      return $arg;
    }

    throw new IllegalArgumentException('Expecting either an iterator, iterable, generator or an array');
  }
}