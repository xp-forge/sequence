<?php namespace util\data;

use lang\IllegalArgumentException;

/**
 * Invocation support: Wraps indirect references to callables - string
 * and array references to functions and methods - in closures for better
 * invocation performance.
 *
 * ```php
 * $f= Closure::of($func);
 * $f();
 *
 * $this->f= Closure::of($func);
 * $this->f->__invoke();
 * ```
 *
 * @see   https://github.com/xp-forge/sequence/pull/1
 * @see   php://language.types.callable
 * @see   php://reflectionfunction.getclosure
 * @see   php://reflectionmethod.getclosure
 * @test  xp://util.data.unittest.ClosureTest
 */
abstract class Closure extends \lang\Object {

  /**
   * Verifies a given argument is callable and returns a Closure
   *
   * @param  function(?): var $arg
   * @return php.Closure
   * @throws lang.IllegalArgumentException
   */
  public static function of($arg, $type= null) {
    if (null === $arg) throw new IllegalArgumentException('Null argument is not callable');

    try {
      return null === $type ? Functions::$ANY->cast($arg) : $type->cast($arg);
    } catch (\lang\ClassCastException $e) {
      throw new IllegalArgumentException('Argument is not callable', $e);
    }
  }
}