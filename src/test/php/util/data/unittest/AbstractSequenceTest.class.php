<?php namespace util\data\unittest;

use lang\Object;
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
   * Unary operator
   *
   * @param  var $in
   * @return var
   */
  public function unaryop($in) { return $in; }

  /**
   * Returns valid unary operators (instances of `function(var): var`)
   *
   * @return var[][]
   */
  protected function unaryops() {
    return [
      [function($in) { return $in; }, 'closure'],
      [[$this, 'unaryop'], 'method'],
      [['xp', 'typeOf'], 'static-method'],
      ['xp::typeOf', 'static-method-string'],
      ['typeof', 'function']
    ];
  }

  /**
   * Supplier
   *
   * @return var
   */
  public function supplier() { return 1; }

  /**
   * Returns valid suppliers (instances of `function(): var`)
   *
   * @return var[][]
   */
  protected function suppliers() {
    return [
      [function() { return 1; }, 'closure'],
      [[$this, 'supplier'], 'method'],
      [['xp', 'gc'], 'static-method'],
      ['xp::gc', 'static-method-string'],
      ['time', 'function']
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
      [new Object()], [new Name('...')], [$this],
      [[]], [[$this]], [['xp']], [['xp', 'g']], [[$this, 'getName', 'excess-element']],
      ['xp:g'], ['xp::'], ['xp::g'], ['::gc'], ['typeo']
    ];
  }
}