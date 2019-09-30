<?php namespace util\data\unittest;

use lang\IllegalArgumentException;
use util\data\{Enumeration, Sequence};

class EnumerationTest extends \unittest\TestCase {

  #[@test, @values('util.data.unittest.Enumerables::validArrays')]
  public function all_in_array($enumerable, $desc) {
    $result= [];
    foreach (Enumeration::of($enumerable) as $value) {
      $result[]= $value;
    }
    $this->assertEquals([1, 2, 3], $result, $desc);
  }

  #[@test, @values('util.data.unittest.Enumerables::validMaps')]
  public function all_in_map($enumerable, $desc) {
    $result= [];
    foreach (Enumeration::of($enumerable) as $key => $value) {
      $result[$key]= $value;
    }
    $this->assertEquals(['color' => 'green', 'price' => 12.99], $result, $desc);
  }

  #[@test]
  public function all_in_sequence() {
    $result= [];
    foreach (Enumeration::of(Sequence::of([1, 2, 3])) as $value) {
      $result[]= $value;
    }
    $this->assertEquals([1, 2, 3], $result);
  }

  #[@test]
  public function lazy_init_via_function() {
    $result= [];
    foreach (Enumeration::of(function() { return [1, 2, 3]; }) as $value) {
      $result[]= $value;
    }
    $this->assertEquals([1, 2, 3], $result);
  }

  #[@test, @values('util.data.unittest.Enumerables::invalid'), @expect(IllegalArgumentException::class)]
  public function raises_exception_when_given($value) {
    Enumeration::of($value);
  }

  #[@test]
  public function returns_empty_enumerable_for_null() {
    $this->assertEquals([], Enumeration::of(null));
  }

  #[@test, @expect(IllegalArgumentException::class)]
  public function returns_empty_enumerable_for_non_iterables() {
    Enumeration::of('a string');
  }
}