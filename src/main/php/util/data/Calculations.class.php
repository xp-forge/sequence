<?php namespace util\data;

use util\Comparator;

class Calculations {

  public static function min($comparator= null) {
    if (null === $comparator) {
      $accumulator= function(&$result, $arg) { if (null === $result || $arg < $result) $result= $arg; };
    } else {
      $f= Functions::$COMPARATOR->newInstance($comparator instanceof Comparator ? [$comparator, 'compare'] : $comparator);
      $accumulator= function(&$result, $arg) use($f) { if (null === $result || $f($arg, $result) < 0) $result= $arg; };
    }
    return new Collector(function() { return null; }, $accumulator);
  }

  public static function max($comparator= null) {
    if (null === $comparator) {
      $accumulator= function(&$result, $arg) { if (null === $result || $arg > $result) $result= $arg; };
    } else {
      $f= Functions::$COMPARATOR->newInstance($comparator instanceof Comparator ? [$comparator, 'compare'] : $comparator);
      $accumulator= function(&$result, $arg) use($f) { if (null === $result || $f($arg, $result) > 0) $result= $arg; };
    }
    return new Collector(function() { return null; }, $accumulator);
  }

  public static function average($num= null) { return Collectors::averaging($num); }

  public static function count() { return Collectors::counting(); }
}