<?php namespace util\data\unittest;

use util\XPIterator;
use util\data\Sequence;

/**
 * Valid and invalid enumerables used as values for sequence and enumeration
 * tests. Defines PHP 5.5 generator fixtures for forward compatibility. Since
 * their definition involves new syntax unparseable with previous PHP versions, 
 * wrapped in eval() statements.
 *
 * @see   xp://util.data.unittest.EnumerationTest
 * @see   xp://util.data.unittest.AbstractSequenceTest
 * @see   php://generators
 */
abstract class Enumerables {

  /**
   * Returns valid arguments for the `of()` method.
   *
   * @return var[][]
   */
  public static function valid() {
    return array_merge(self::validArrays(), self::validMaps());
  }

  /**
   * Returns valid arguments for the `of()` method: Arrays, iterables, 
   * iterators and generators.
   *
   * @return var[][]
   */
  public static function validArrays() {
    return array_merge(self::fixedArrays(), self::streamedArrays());
  }

  /**
   * Returns valid arguments for the `of()` method: Maps, iterables, 
   * iterators and generators.
   *
   * @return var[][]
   */
  public static function validMaps() {
    return array_merge(self::fixedMaps(), self::streamedMaps());
  }

  /**
   * Returns fixed enumerables, that is, those that can be rewound.
   *
   * @return var[][]
   */
  public static function fixedArrays() {
    return [
      [[1, 2, 3], 'array'],
      [new \ArrayObject([1, 2, 3]), 'fixed-iterable'],
      [new \ArrayIterator([1, 2, 3]), 'rewindable-iterator'],
      [Sequence::of([1, 2, 3]), 'self-of-fixed-enumerable'],
    ];
  }

  /**
   * Returns streamed enumerables, that is, those that cannot be rewound.
   *
   * @return var[][]
   */
  public static function streamedArrays() {
    $f= function() { yield 1; yield 2; yield 3; };
    return [
      [$f, 'closure'],
      [$f(), 'generator'],
      [newinstance(XPIterator::class, [], '{
        protected $numbers= [1, 2, 3];
        public function hasNext() { return !empty($this->numbers); }
        public function next() { return array_shift($this->numbers); }
      }'), 'xp-iterator'],
      [Sequence::of(newinstance(XPIterator::class, [], '{
        protected $numbers= [1, 2, 3];
        public function hasNext() { return !empty($this->numbers); }
        public function next() { return array_shift($this->numbers); }
      }')), 'self-of-xp-iterator']
    ];
  }

  /**
   * Returns fixed enumerables, that is, those that can be rewound.
   *
   * @return var[][]
   */
  public static function fixedMaps() {
    return [
      [['color' => 'green', 'price' => 12.99], 'map'],
      [new \ArrayObject(['color' => 'green', 'price' => 12.99]), 'iterable'],
      [new \ArrayIterator(['color' => 'green', 'price' => 12.99]), 'iterator'],
      [Sequence::of(['color' => 'green', 'price' => 12.99]), 'self']
    ];
  }

  /**
   * Returns streamed enumerables, that is, those that cannot be rewound.
   *
   * @return var[][]
   */
  public static function streamedMaps() {
    $f= function() { yield 'color' => 'green'; yield 'price' => 12.99; };
    return [[$f, 'closure'], [$f(), 'generator']];
  }
  /**
   * Returns invalid arguments for the `of()` method: Primitives, non-iterable
   * objects, and a function which is not a generator.
   *
   * @return var[][]
   */
  public static function invalid() {
    return [
      [''], ['...'], [-1], [0], [1], [0.5], [false], [true],
      [new Name('...')],
      [function() { return 1; }]
    ];
  }
}