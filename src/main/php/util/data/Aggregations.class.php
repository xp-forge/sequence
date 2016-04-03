<?php namespace util\data;

use util\Comparator;

/**
 * Aggregations factory
 *
 * @see   xp://util.data.ICollector
 * @test  xp://util.data.unittest.AggregationsTest
 */
final class Aggregations {

  private function __construct() { }

  /**
   * Returns the smallest element. Optimized for the case when the no comparator
   * is given, using the `<` operator.
   *
   * @param  var $comparator default NULL Either a Comparator or a closure to compare.
   * @return util.data.ICollector
   */
  public static function min($comparator= null) {
    if (null === $comparator) {
      $accumulator= function(&$result, $arg) { if (null === $result || $arg < $result) $result= $arg; };
    } else {
      $f= Functions::$COMPARATOR->newInstance($comparator instanceof Comparator ? [$comparator, 'compare'] : $comparator);
      $accumulator= function(&$result, $arg) use($f) { if (null === $result || $f($arg, $result) < 0) $result= $arg; };
    }
    return new Collector(function() { return null; }, $accumulator);
  }

  /**
   * Returns the largest element. Optimized for the case when no comparator is 
   * given, using the `>` operator.
   *
   * @param  var $comparator default NULL Either a Comparator or a closure to compare.
   * @return util.data.ICollector
   */
  public static function max($comparator= null) {
    if (null === $comparator) {
      $accumulator= function(&$result, $arg) { if (null === $result || $arg > $result) $result= $arg; };
    } else {
      $f= Functions::$COMPARATOR->newInstance($comparator instanceof Comparator ? [$comparator, 'compare'] : $comparator);
      $accumulator= function(&$result, $arg) use($f) { if (null === $result || $f($arg, $result) > 0) $result= $arg; };
    }
    return new Collector(function() { return null; }, $accumulator);
  }

  /**
   * Creates a new collector to sum up elements. Uses the given function to produce a 
   * number for each element. If omitted, uses the elements themselves.
   *
   * @param  function(var): var $num
   * @return util.data.ICollector
   */
  public static function sum($num= null) {
    if (null === $num) {
      $accumulator= function(&$result, $arg) { $result+= $arg; };
    } else {
      $func= Functions::$APPLY->newInstance($num);
      $accumulator= function(&$result, $arg) use($func) { $result+= $func($arg); }; 
    }

    return new Collector(function() { return 0; }, $accumulator);
  }

  /**
   * Creates a new collector to calculate an average for all the given elements. Uses
   * the given function to produce a number for each element. If omitted, uses the
   * elements themselves.
   *
   * @param  function(var): var $num
   * @return util.data.ICollector
   */
  public static function average($num= null) {
    if (null === $num) {
      $accumulator= function(&$result, $arg) { $result[0]+= $arg; $result[1]++; };
    } else {
      $f= Functions::$APPLY->newInstance($num);
      $accumulator= function(&$result, $arg) use($f) { $result[0]+= $f($arg); $result[1]++;  };
    }

    return new Collector(
      function() { return [0, 0]; },
      $accumulator,
      function($result) { return $result[1] ? $result[0] / $result[1] : null; }
    );
  }

  /**
   * Counts all elements
   *
   * @return util.data.ICollector
   */
  public static function count() {
    return new Collector(
      function() { return 0; },
      function(&$result, $arg) { $result++; }
    );
  }
}