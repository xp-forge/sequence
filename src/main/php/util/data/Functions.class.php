<?php namespace util\data;

use lang\FunctionType;
use lang\Type;
use lang\Primitive;

/**
 * Invocation support
 *
 * @see  http://docs.oracle.com/javase/8/docs/api/java/util/function/package-summary.html
 */
abstract class Functions extends \lang\Object {
  public static $PREDICATE;
  public static $SUPPLIER;
  public static $CONSUMER;
  public static $MAPPER;
  public static $COMPARATOR;
  public static $ANY;

  static function __static() {
    self::$PREDICATE= new FunctionType([Type::$VAR], Primitive::$BOOL);
    self::$SUPPLIER= new FunctionType([], Type::$VAR);
    self::$CONSUMER= new FunctionType([Type::$VAR], Type::$VOID);
    self::$MAPPER= new FunctionType([Type::$VAR], Type::$VAR);
    self::$COMPARATOR= new FunctionType([Type::$VAR, Type::$VAR], Primitive::$INT);
    self::$ANY= new FunctionType(null, Type::$VAR);
  }
}