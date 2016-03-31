<?php namespace util\data;

class Calculations {

  public static function min(&$return) {
    $min= null;
    return function($value) use(&$min, &$return) {
      if (null === $min || $value < $min) $min= $return= $value; return true;
    };
  }

  public static function max(&$return) {
    $max= null;
    return function($value) use(&$max, &$return) {
      if (null === $max || $value > $max) $max= $return= $value; return true;
    };
  }

  public static function average(&$return) {
    $count= 0;
    $sum= 0;
    return function($value) use(&$count, &$sum, &$return) {
      $sum+= $value; $count++; $return= $sum / $count; return true;
    };
  }
}