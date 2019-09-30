<?php namespace util\data;

use lang\{FunctionType, Primitive, Type, Wildcard};

/**
 * Function types used throughout library
 */
abstract class Functions {
  public static $SUPPLY, $CONSUME, $CONSUME_WITH_KEY, $UNARYOP, $BINARYOP, $COMPARATOR, $APPLY, $APPLY_WITH_KEY, $RECV, $RECV_WITH_KEY;

  static function __static() {
    self::$SUPPLY= new FunctionType([], Type::$VAR);
    self::$CONSUME= new FunctionType([Type::$VAR, Type::$VAR], Type::$VOID);
    self::$CONSUME_WITH_KEY= new FunctionType([Type::$VAR, Type::$VAR, Type::$VAR], Type::$VOID);
    self::$UNARYOP= new FunctionType([Type::$VAR], Type::$VAR);
    self::$BINARYOP= new FunctionType([Type::$VAR, Type::$VAR], Type::$VAR);
    self::$COMPARATOR= new FunctionType([Type::$VAR, Type::$VAR], Primitive::$INT);
    self::$APPLY= new FunctionType(null, Type::$VAR);
    self::$APPLY_WITH_KEY= new FunctionType([Type::$VAR, Type::$VAR], Type::$VAR);
    self::$RECV= new FunctionType(null, Wildcard::$ANY);
    self::$RECV_WITH_KEY= new FunctionType([Type::$VAR, Type::$VAR], Wildcard::$ANY);
  }
}