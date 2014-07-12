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
   * @param  function<var..., var> $arg
   * @return php.Closure
   * @throws lang.IllegalArgumentException
   */
  public static function of($arg) {
    if ($arg instanceof \Closure) {
      return $arg;
    } else if (is_string($arg)) {
      try {
        if (1 === sscanf($arg, '%[^:]::%s', $class, $method)) {
          return (new \ReflectionFunction($arg))->getClosure();
        } else {
          $class= strtr($class, '.', '\\');
          if (method_exists($class, $method)) {
            return (new \ReflectionMethod($class, $method))->getClosure(null);
          } else if (method_exists($class, '__callStatic')) {
            return function() use($class, $method) {
              return call_user_func([$class, '__callStatic'], $method, func_get_args());
            };
          }
        }
      } catch (\Exception $e) {
        throw new IllegalArgumentException($e->getMessage());
      }
    } else if (is_array($arg) && 2 === sizeof($arg) && is_string($arg[1])) {
      try {
        $method= $arg[1];
        if (is_object($arg[0])) {
          $instance= $arg[0];
          if (method_exists($instance, $method)) {
            return (new \ReflectionMethod($instance, $method))->getClosure($instance);
          } else if (method_exists($instance, '__call')) {
            return function() use($instance, $method) {
              return call_user_func([$instance, '__call'], $method, func_get_args());
            };
          }
        } else if (is_string($arg[0])) {
          $class= strtr($arg[0], '.', '\\');
          if (method_exists($class, $method)) {
            return (new \ReflectionMethod($class, $method))->getClosure(null);
          } else if (method_exists($class, '__callStatic')) {
            return function() use($class, $method) {
              return call_user_func([$class, '__callStatic'], $method, func_get_args());
            };
          }
        }
      } catch (\Exception $e) {
        throw new IllegalArgumentException($e->getMessage());
      }
    }

    throw new IllegalArgumentException('Argument is not callable');
  }
}