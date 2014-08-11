<?php namespace util\data\unittest;

use lang\types\ArrayList;
use lang\types\String;
use lang\Object;
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
abstract class Enumerables extends Object {
  private static $generators;

  #[@beforeClass]
  static function __static() {
    self::$generators= class_exists('Generator', false);
  }

  /**
   * Returns valid arguments for the `of()` method: Arrays, iterables, 
   * iterators and generators (the latter only if available).
   *
   * @return var[][]
   */
  public static function valid() {
    return array_merge(
      self::$generators ? [
        [eval('return function() { yield 1; yield 2; yield 3; };'), 'closure'],
        [eval('$f= function() { yield 1; yield 2; yield 3; }; return $f();'), 'generator']
      ] : [],
      [
        [[1, 2, 3], 'array'],
        [new ArrayList(1, 2, 3), 'iterable'],
        [new \ArrayIterator([1, 2, 3]), 'iterator'],
        [newinstance('util.XPIterator', [], '{
          protected $numbers= [1, 2, 3];
          public function hasNext() { return $this->numbers; }
          public function next() { return array_shift($this->numbers); }
        }'), 'xp-iterator'],
        [Sequence::of([1, 2, 3]), 'self'],
      ]
    );
  }

  /**
   * Returns fixed enumerables, that is, those that can be rewound.
   *
   * @return var[][]
   */
  public static function fixed() {
    return [
      [[1, 2, 3], 'array'],
      [new ArrayList(1, 2, 3), 'fixed-iterable'],
      [new \ArrayIterator([1, 2, 3]), 'rewindable-iterator'],
      [Sequence::of([1, 2, 3]), 'self-of-fixed-enumerable'],
    ];
  }

  /**
   * Returns streamed enumerables, that is, those that cannot be rewound.
   *
   * @return var[][]
   */
  public static function streamed() {
    return array_merge(
      self::$generators ? [
        [eval('return function() { yield 1; yield 2; yield 3; };'), 'closure'],
        [eval('$f= function() { yield 1; yield 2; yield 3; }; return $f();'), 'generator']
      ] : [],
      [
        [newinstance('util.XPIterator', [], '{
          protected $numbers= [1, 2, 3];
          public function hasNext() { return $this->numbers; }
          public function next() { return array_shift($this->numbers); }
        }'), 'xp-iterator'],
        [Sequence::of(newinstance('util.XPIterator', [], '{
          protected $numbers= [1, 2, 3];
          public function hasNext() { return $this->numbers; }
          public function next() { return array_shift($this->numbers); }
        }')), 'self-of-xp-iterator']
      ]
    );
  }

  /**
   * Returns invalid arguments for the `of()` method: Primitives, non-iterable
   * objects, and a function which is not a generator.
   *
   * @return var[][]
   */
  public static function invalid() {
    return [
      [null], [''], ['...'], [-1], [0], [1], [0.5], [false], [true],
      [new Object()], [new String('...')],
      [function() { return 1; }]
    ];
  }
}