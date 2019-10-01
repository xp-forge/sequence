<?php namespace util\data\unittest;

use unittest\Assert;
use util\data\Collector;
use util\data\Sequence;

abstract class AbstractSequenceTest {

  /**
   * Assertion helper
   *
   * @param  var[] $expected
   * @param  util.data.Sequence $sequence
   * @param  string $message
   * @throws unittest.AssertionFailedError
   */
  protected function assertSequence($expected, $sequence, $message= '!=') {
    Assert::equals($expected, $sequence->toArray(), $message);
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
      [[Methods::class, 'unaryop'], 'static-method'],
      ['util.data.unittest.Methods::unaryop', 'static-method-string'],
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
      [[Methods::class, 'supplier'], 'static-method'],
      ['util.data.unittest.Methods::supplier', 'static-method-string'],
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
      [new Name('...')], [$this],
      [[]], [[$this]], [['xp']], [['xp', 'g']], [[$this, 'getName', 'excess-element']],
      ['xp:g'], ['xp::'], ['xp::g'], ['::gc'], ['typeo']
    ];
  }
}