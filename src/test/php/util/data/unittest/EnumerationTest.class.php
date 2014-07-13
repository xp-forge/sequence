<?php namespace util\data\unittest;

use util\data\Enumeration;
use util\data\Sequence;
use lang\types\ArrayList;

class EnumerationTest extends \unittest\TestCase {
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
   * Returns valid arguments for the `of()` method: Arrays, iterables, 
   * iterators and generators (the latter only if available).
   *
   * @return var[][]
   */
  protected function valid() {
    return array_merge(self::$generators, [
      [[1, 2, 3], 'array'],
      [new ArrayList(1, 2, 3), 'iterable'],
      [new \ArrayIterator([1, 2, 3]), 'iterator'],
      [newinstance('util.XPIterator', [], [
        'numbers' => [1, 2, 3],
        'hasNext' => function() { return $this->numbers; },
        'next'    => function() { return array_shift($this->numbers); }
      ]), 'xp-iterator'],
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
      [new \lang\Object()], [new \lang\types\String('...')], [$this],
      [function() { return 1; }]
    ];
  }

  #[@test, @values('valid')]
  public function all_in($enumerable) {
    $result= [];
    foreach (Enumeration::of($enumerable) as $value) {
      $result[]= $value;
    }
    $this->assertEquals([1, 2, 3], $result);
  }

  #[@test, @values('invalid'), @expect('lang.IllegalArgumentException')]
  public function raises_exception_when_given($value) {
    Enumeration::of($value);
  }
}