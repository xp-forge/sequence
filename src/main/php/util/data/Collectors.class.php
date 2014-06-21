<?php namespace util\data;

use lang\XPClass;
use util\collections\Vector;
use util\collections\HashTable;
use util\collections\HashSet;

final class Collectors extends \lang\Object {

  /**
   * Creates a new collector gathering the elements in a list
   *
   * @return util.data.ICollector
   */
  public static function toList() {
    return new Collector(
      function() { return new Vector(); },
      function($result, $arg) { $result->add($arg); }
    );
  }

  /**
   * Creates a new collector gathering the elements in a list
   *
   * @return util.data.ICollector
   */
  public static function toSet() {
    return new Collector(
      function() { return new HashSet(); },
      function($result, $arg) { $result->add($arg); }
    );
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
   * @param  function<var, var> $key
   * @param  function<var, var> $value
   * @return util.data.ICollector
   */
  public static function toMap($key, $value) {
    return new Collector(
      function() { return new HashTable(); },
      function($result, $arg) use($key, $value) { $result->put($key($arg), $value($arg)); }
    );
  }

  /**
   * Creates a new collector gathering the elements in a map.
   *
   * @param  function<var, var> $classifier
   * @param  util.data.ICollector $collector
   * @return util.data.ICollector
   */
  public static function groupingBy($classifier, ICollector $collector= null) {
    if (null === $collector) $collector= self::toList();
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
        function($result, $arg) use($classifier, $supplier, $accumulator) {
          $key= $classifier($arg);
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
        function($result, $arg) use($classifier, $supplier, $accumulator) {
          $key= $classifier($arg);
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
   * Adapts a collector in a way that each element gets passed to a given mapper
   * prior to accumulation by the collector.
   *
   * @param  function<var, var> $mapper
   * @param  util.data.ICollector $collector
   * @return util.data.ICollector
   */
  public static function mapping($mapper, ICollector $collector= null) {
    if (null === $collector) $collector= self::toList();
    $accumulator= $collector->accumulator();

    return new Collector(
      $collector->supplier(),
      function($result, $arg) use($mapper, $accumulator) {
        $accumulator($result, $mapper($arg));
      }
    );
  }

  /**
   * Creates a new collector to sum up elements
   *
   * @param  function<var, var> $num
   * @return util.data.ICollector
   */
  public static function summing($num) {
    return new Collector(
      function() { return 0; },
      function(&$result, $arg) use($num) { $result+= $num($arg); }
    );
  }

  /**
   * Creates a new collector to calculate an average for all the given elements
   *
   * @param  function<var, var> $num
   * @return util.data.ICollector
   */
  public static function averaging($num) {
    return new Collector(
      function() { return [0, 0]; },
      function(&$result, $arg) use($num) { $result[0]+= $num($arg); $result[1]++; },
      function($result) { return $result[0] / $result[1]; }
    );
  }

  /**
   * Creates a new collector counting all elements
   *
   * @return util.data.ICollector
   */
  public static function counting() {
    return new Collector(
      function() { return 0; },
      function(&$result, $arg) { $result++; }
    );
  }

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