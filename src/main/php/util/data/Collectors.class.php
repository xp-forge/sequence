<?php namespace util\data;

use lang\XPClass;
use util\collections\Vector;
use util\collections\HashTable;
use util\collections\HashSet;

/**
 * Collector factory
 *
 * @see   xp://util.data.ICollector
 * @test  xp://util.data.unittest.CollectorsTest
 */
final class Collectors extends \lang\Object {

  private function __construct() { }

  /**
   * Creates a new collector gathering the elements in a list
   *
   * @param  function(var): var $value If omitted, element is used in value
   * @return util.data.ICollector
   */
  public static function toList($value= null) {
    if (null === $value) {
      $accumulator= function($result, $arg) { $result->add($arg); };
    } else {
      $func= Functions::$APPLY->newInstance($value);
      $accumulator= function($result, $arg) use($func) { $result->add($func($arg)); };
    }
    return new Collector(function() { return new Vector(); }, $accumulator);
  }

  /**
   * Creates a new collector gathering the elements in a set
   *
   * @param  function(var): var $value If omitted, element is used in value
   * @return util.data.ICollector
   */
  public static function toSet($value= null) {
    if (null === $value) {
      $accumulator= function($result, $arg) { $result->add($arg); };
    } else {
      $func= Functions::$APPLY->newInstance($value);
      $accumulator= function($result, $arg) use($func) { $result->add($func($arg)); };
    }
    return new Collector(function() { return new HashSet(); }, $accumulator);
  }

  /**
   * Creates a new collector gathering the elements in a collection class
   *
   * @param  lang.XPClass $class
   * @return util.data.ICollector
   */
  public static function toCollection(XPClass $class) {
    return new Collector(
      function() use($class) { return $class->newInstance(); },
      function($result, $arg) { $result->add($arg); }
    );
  }

  /**
   * Creates a new collector gathering the elements in a map.
   *
   * @param  function(var): var $key
   * @param  function(var): var $value If omitted, element is used in value
   * @return util.data.ICollector
   */
  public static function toMap($key= null, $value= null) {
    if (null === $key && null === $value) {
      return new Collector(
        function() { return new HashTable(); },
        function($result, $arg, $key) { $result->put($key, $arg); }
      );
    } else if (null === $key) {
      $v= Functions::$APPLY->newInstance($value);
      return new Collector(
        function() { return new HashTable(); },
        function($result, $arg, $key) use($v) { $result->put($key, $v($arg)); }
      );
    } else if (null === $value) {
      $k= Functions::$APPLY->newInstance($key);
      return new Collector(
        function() { return new HashTable(); },
        function($result, $arg) use($k) { $result->put($k($arg), $arg); }
      );
    } else {
      $k= Functions::$APPLY->newInstance($key);
      $v= Functions::$APPLY->newInstance($value);
      return new Collector(
        function() { return new HashTable(); },
        function($result, $arg) use($k, $v) { $result->put($k($arg), $v($arg)); }
      );
    }
  }

  /**
   * Creates a new collector gathering the elements in a map.
   *
   * @param  function(var): var $classifier
   * @param  util.data.ICollector $collector
   * @return util.data.ICollector
   */
  public static function groupingBy($classifier, ICollector $collector= null) {
    if (null === $collector) $collector= self::toList();
    $func= Functions::$APPLY->cast($classifier);
    $supplier= $collector->supplier();
    $accumulator= $collector->accumulator();
    $finisher= $collector->finisher();

    $return= function() { return new HashTable(); };
    if ($finisher) {
      $finish= function($result) use($finisher) {
        foreach ($result as $pair) { $result[$pair->key]= $finisher($pair->value); }
        return $result;
      };
    } else {
      $finish= null;
    }

    $f= new \ReflectionFunction($accumulator);
    if ($f->getNumberOfParameters() > 1 && $f->getParameters()[0]->isPassedByReference()) {
      return new Collector(
        $return,
        function($result, $arg) use($func, $supplier, $accumulator) {
          $key= $func($arg);
          if ($result->containsKey($key)) {
            $value= $result->get($key);
          } else {
            $value= $supplier();
          }
          $accumulator($value, $arg);
          @$result->put($key, $value);
        },
        $finish
      );
    } else {
      return new Collector(
        $return,
        function($result, $arg) use($func, $supplier, $accumulator) {
          $key= $func($arg);
          if ($result->containsKey($key)) {
            $accumulator($result->get($key), $arg);
          } else {
            $value= $supplier();
            $accumulator($value, $arg);
            $result->put($key, $value);
          }
        },
        $finish
      );
    }
  }

  /**
   * Creates a new collector gathering the elements in a map.
   *
   * @param  function(var): var $predicate
   * @return util.data.ICollector
   */
  public static function partitioningBy($predicate, ICollector $collector= null) {
    if (null === $collector) $collector= self::toList();
    $func= Functions::$APPLY->cast($predicate);
    $supplier= $collector->supplier();
    $accumulator= $collector->accumulator();

    return new Collector(
      function() use($supplier) {
        $result= new HashTable();
        $result[true]= $supplier();
        $result[false]= $supplier();
        return $result;
      },
      function($result, $arg) use($func, $accumulator) {
        $accumulator($result[(bool)$func($arg)], $arg);
      }
    );
  }

  /**
   * Adapts a collector in a way that each element gets passed to a given mapper
   * prior to accumulation by the collector.
   *
   * @param  function(var): var $mapper
   * @param  util.data.ICollector $collector
   * @return util.data.ICollector
   */
  public static function mapping($mapper, ICollector $collector= null) {
    if (null === $collector) $collector= self::toList();
    $func= Functions::$APPLY->newInstance($mapper);
    $accumulator= $collector->accumulator();

    return new Collector(
      $collector->supplier(),
      function($result, $arg) use($func, $accumulator) {
        $accumulator($result, $func($arg));
      }
    );
  }

  /**
   * Creates a new collector to sum up elements. Uses the given function to produce a 
   * number for each element. If omitted, uses the elements themselves.
   *
   * @param  function(var): var $num
   * @return util.data.ICollector
   */
  public static function summing($num= null) {
    if (null === $num) {
      $accumulator= function(&$result, $arg) use($num) { $result+= $arg; };
    } else {
      $func= Functions::$APPLY->newInstance($num);
      $accumulator= function(&$result, $arg) use($func) { $result+= $func($arg); }; 
    }

    return new Collector(
      function() { return 0; },
      $accumulator
    );
  }

  /**
   * Creates a new collector to calculate an average for all the given elements. Uses
   * the given function to produce a number for each element. If omitted, uses the
   * elements themselves.
   *
   * @param  function(var): var $num
   * @return util.data.ICollector
   */
  public static function averaging($num= null) { return Calculations::average($num); }

  /**
   * Creates a new collector counting all elements
   *
   * @return util.data.ICollector
   */
  public static function counting() { return Calculations::count(); }

  /**
   * Creates a new collector to join elements
   *
   * @param  string $delimiter
   * @param  string $prefix
   * @param  string $suffix
   * @return util.data.ICollector
   */
  public static function joining($delimiter= ', ', $prefix= '', $suffix= '') {
    return new Collector(
      function() { return null; },
      function(&$result, $arg) use($prefix, $delimiter) {
        if (null === $result) {
          $result= $prefix.$arg;
        } else {
          $result.= $delimiter.$arg;
        }
      },
      function($result) use($suffix) { return $result.$suffix; }
    );
  }
}