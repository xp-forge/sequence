<?php namespace util\data;

class Calculations {

  public static function min() {
    return new Collector(
      function() { return null; },
      function(&$result, $arg) { if (null === $result || $arg < $result) $result= $arg; }
    );
  }

  public static function max() {
    return new Collector(
      function() { return null; },
      function(&$result, $arg) { if (null === $result || $arg > $result) $result= $arg; }
    );
  }

  public static function average($num= null) { return Collectors::averaging($num); }

  public static function count() { return Collectors::counting(); }
}