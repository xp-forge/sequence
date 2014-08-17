<?php namespace util\data\unittest;

use lang\types\String;
use lang\types\ArrayList;
use util\data\Sequence;
use util\data\Collector;

abstract class AbstractSequenceTest extends \unittest\TestCase {

  /**
   * Assertion helper
   *
   * @param  var[] $expected
   * @param  util.data.Sequence $sequence
   * @param  string $message
   * @throws unittest.AssertionFailedError
   */
  protected function assertSequence($expected, $sequence, $message= '!=') {
    $this->assertEquals($expected, $sequence->toArray(), $message);
  }

  /**
   * Returns valid arguments for the `iterate()` and `generate()` methods.
   *
   * @return var[][]
   */
  protected function callables() {
    return [
      [function() { return 1; }, 'closure'],
      [[$this, 'getName'], 'method'],
      [['xp', 'gc'], 'static-method'],
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