<?php namespace util\data;

use lang\IllegalArgumentException;
use lang\FunctionType;
use lang\Type;
use lang\Primitive;

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
  public static $APPLY, $APPLY_WITH_KEY;

  static function __static() {
    self::$APPLY= new FunctionType([Type::$VAR], Type::$VAR);
    self::$APPLY_WITH_KEY= new FunctionType([Type::$VAR, Type::$VAR], Type::$VAR);
  }

  /**
   * Returns a closure for a given method
   *
   * @param  var $instance
   * @param  string $method
   * @return php.Closure
   * @throws lang.IllegalArgumentException
   */
  protected static function methodClosure($instance, $method) {
    if (method_exists($instance, $method)) {
      return (new \ReflectionMethod($instance, $method))->getClosure($instance);
    } else if (method_exists($instance, '__call')) {
      return function() use($instance, $method) {
        return $instance->__call($method, func_get_args());
      };
    } else {
      throw new IllegalArgumentException(sprintf(
        'Neither found a method %s() nor a call handler in %s instance',
        $method,
        \xp::reflect(get_class($instance))
      ));
    }
  }

  /**
   * Returns a closure for a given static method
   *
   * @param  string $class
   * @param  string $method
   * @return php.Closure
   * @throws lang.IllegalArgumentException
   */
  protected static function staticMethodClosure($class, $method) {
    $name= strtr($class, '.', '\\');
    if (method_exists($name, $method)) {
      $m= new \ReflectionMethod($name, $method);
      if ($m->isStatic()) {
        return $m->getClosure(null);
      } else {
        throw new IllegalArgumentException('Method '.$class.'::'.$method.'() does not reference a static method');
      }
    } else if (method_exists($name, '__callStatic')) {
      return function() use($name, $method) {
        return $name::__callStatic($method, func_get_args());
      };
    } else if (class_exists($name, false)) {
      throw new IllegalArgumentException(sprintf(
        'Neither found a method %s() nor a call handler in %s',
        $method,
        $class
      ));
    } else {
      throw new IllegalArgumentException('No such class '.$class);
    }
  }

  /**
   * Verifies a given argument is callable and returns a Closure
   *
   * @param  function(var...): var $arg
   * @return php.Closure
   * @throws lang.IllegalArgumentException
   */
  public static function of($arg) {
    try {
      if ($arg instanceof \Closure) {
        return $arg;
      } else if (is_string($arg)) {
        if (1 === sscanf($arg, '%[^:]::%s', $class, $method)) {
          return (new \ReflectionFunction($arg))->getClosure();
        } else {
          return self::staticMethodClosure($class, $method);
        }
      } else if (is_array($arg) && 2 === sizeof($arg) && is_string($arg[1])) {
        if (is_object($arg[0])) {
          return self::methodClosure($arg[0], $arg[1]);
        } else if (is_string($arg[0])) {
          return self::staticMethodClosure($arg[0], $arg[1]);
        }
      }
    } catch (\ReflectionException $e) {
      throw new IllegalArgumentException($e->getMessage());
    }

    throw new IllegalArgumentException('Argument is not callable');
  }
}