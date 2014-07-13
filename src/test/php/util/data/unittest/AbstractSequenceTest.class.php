<?php namespace util\data\unittest;

use lang\types\String;
use lang\types\ArrayList;
use util\data\Sequence;
use util\data\Collector;

abstract class AbstractSequenceTest extends \unittest\TestCase {

  /**
   * Returns valid arguments for the `iterate()` and `generate()` methods.
   *
   * @return var[][]
   */
  protected function callables() {
    return [
      [function() { return 1; }, 'closure'],
      [[$this, 'getName'], 'method'], [[$this, 'nonExistant'], '__call'],
      [['xp', 'gc'], 'static-method'], [[__CLASS__, 'nonExistant'], '__callStatic'],
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