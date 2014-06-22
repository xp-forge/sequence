<?php namespace util\data\unittest;

use lang\types\String;
use util\data\Sequence;
use util\data\Collector;

abstract class AbstractSequenceTest extends \unittest\TestCase {
  private static $generators= [];

  /**
   * Defines generator fixtures. Since their definition involves new syntax
   * unparseable with previous PHP versions, wrap in eval() statements.
   *
   * @see   php://generators
   */
  #[@beforeClass]
  public static function defineGenerators() {
    if (class_exists('Generator', false) && !self::$generators) {
      self::$generators= [
        [eval('return function() { yield 1; yield 2; yield 3; };'), 'closure'],
        [eval('$f= function() { yield 1; yield 2; yield 3; }; return $f();'), 'generator']
      ];
    }
  }

  /**
   * Returns valid arguments for the `of()` method: Arrays, iterables, and
   * generators (the latter only if available in the underlying runtime).
   *
   * @return var[][]
   */
  protected function valid() {
    return array_merge(self::$generators, [
      [[1, 2, 3], 'array'],
      [new \lang\types\ArrayList(1, 2, 3), 'iterable'],
      [Sequence::of([1, 2, 3]), 'self'],
    ]);
  }

  /**
   * Returns invalid arguments for the `of()` method: Primitives, non-iterable
   * objects, and a function which is not a generator.
   *
   * @return var[][]
   */
  protected function invalid() {
    return [
      [null], [''], ['...'], [-1], [0], [1], [0.5], [false], [true],
      [new \lang\Object()], [new String('...')], [$this],
      [function() { return 1; }]
    ];
  }

  /**
   * Returns valid arguments for the `iterate()` and `generate()` methods.
   *
   * @return var[][]
   */
  protected function callables() {
    return [
      [function() { return 1; }, 'closure'],
      [[$this, 'getName'], 'method'], [[$this, 'nonExistant'], 'via__call'],
      [['xp', 'gc'], 'static-method'], [['self', 'nonExistant'], '__callStatic'],
      ['xp::gc', 'static-method-string'], ['typeof', 'function']
    ];
  }

  /**
   * Returns invalid arguments for the `iterate()` and `generate()` methods.
   *
   * @return var[][]
   */
  protected function noncallables() {
    return [
      [null], [''], ['...'], [-1], [0], [1], [0.5], [false], [true],
      [new \lang\Object()], [new String('...')], [$this],
      [[]], [[$this]], [['xp']], [['xp', 'g']], [[$this, 'getName', 'excess-element']],
      ['xp:g'], ['xp::'], ['xp::g'], ['::gc'], ['typeo']
    ];
  }
}