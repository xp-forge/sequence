<?php namespace util\data\unittest;

use util\data\Enumeration;

class EnumerationTest extends \unittest\TestCase {

  #[@test, @values('util.data.unittest.Enumerables::valid')]
  public function all_in($enumerable, $desc) {
    $result= [];
    foreach (Enumeration::of($enumerable) as $value) {
      $result[]= $value;
    }
    $this->assertEquals([1, 2, 3], $result, $desc);
  }

  #[@test, @values('util.data.unittest.Enumerables::invalid'), @expect('lang.IllegalArgumentException')]
  public function raises_exception_when_given($value) {
    Enumeration::of($value);
  }
}