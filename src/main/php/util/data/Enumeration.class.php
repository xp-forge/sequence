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

  /**
   * Verifies a given argument is an enumeration
   *
   * @param  var $arg
   * @return var
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
      $generator= $arg();
      if ($generator instanceof \Generator) {
        return new YieldingOf($generator);
      }
    } else if ($arg instanceof XPIterator) {
      return new XPIteratorAdapter($arg);
    } else if (is_array($arg)) {
      return $arg;
    }

    throw new IllegalArgumentException('Expecting either an iterator, iterable, generator or an array');
  }
}