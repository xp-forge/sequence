<?php namespace util\data;

use lang\IllegalArgumentException;

/**
 * Invocation support for anything callable
 *
 * ```php
 * $f= Closure::of($func);
 * $f();
 *
 * $this->f= Closure::of($func);
 * $this->f->__invoke();
 * ```
 *
 * @see   php://language.types.callable
 * @see   php://reflectionfunction.getclosure
 * @see   php://reflectionmethod.getclosure
 */
abstract class Closure extends \lang\Object {
  protected static $VARIADIC_SUPPORTED;

  static function __static() {
    self::$VARIADIC_SUPPORTED= method_exists('ReflectionFunction', 'isVariadic');
  }

  /**
   * Wrap internal functions in a dynamic wrapper instead of invoking
   * through the closure returned by ReflectionFunction::getClosure().
   * The latter will pass through excess arguments, which is a problem
   * for functions such as `strlen()` which expect an *exact* amount of
   * arguments.
   *
   * @param  php.ReflectionFunction f
   * @return php.Closure
   */
  protected static function wrapInternal($f) {
    static $wrap= [];

    $sig= $pass= '';
    foreach ($f->getParameters() as $i => $param) {
      $sig.= ', '.($param->isPassedByReference() ? '&' : '').'$_'.$i.($param->isOptional() ? '= null' : '');
      $pass.= ', $_'.$i;
    }

    if (!isset($wrap[$f->name])) {
      $wrap[$f->name]= eval('return function('.substr($sig, 2).') { return '.$f->name.'('.substr($pass, 2).'); };');
    }
    return $wrap[$f->name];
  }

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
          $f= new \ReflectionFunction($arg);
          if ($f->isInternal() && !(self::$VARIADIC_SUPPORTED && $f->isVariadic())) {
            return self::wrapInternal($f);
          } else {
            return $f->getClosure();
          }
        } else if (method_exists($class, $method)) {
          return (new \ReflectionMethod($class, $method))->getClosure(null);
        } else if (method_exists($class, '___callStatic')) {
          return function() use($class, $method) {
            $args= func_get_args();
            return call_user_func([$class, '___callStatic'], [$method, $args]);
          };
        }
      } catch (\Exception $e) {
        throw new IllegalArgumentException($e->getMessage());
      }
    } else if (is_array($arg) && 2 === sizeof($arg)) {
      try {
        if (method_exists($arg[0], $arg[1])) {
          return (new \ReflectionMethod($arg[0], $arg[1]))->getClosure(is_object($arg[0]) ? $arg[0] : null);
        } else if (is_object($arg[0]) && method_exists($arg[0], '__call')) {
          return function() use($arg) {
            $args= func_get_args();
            return call_user_func([$arg[0], '__call'], [$arg[1], $args]);
          };
        } else if (method_exists($arg[0], '__callStatic')) {
          return function() use($arg) {
            $args= func_get_args();
            return call_user_func([$arg[0], '__callStatic'], [$arg[1], $args]);
          };
        }
      } catch (\Exception $e) {
        throw new IllegalArgumentException($e->getMessage());
      }
    }

    throw new IllegalArgumentException('Argument is not callable');
  }
}