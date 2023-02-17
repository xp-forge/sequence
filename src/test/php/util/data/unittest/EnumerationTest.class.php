<?php namespace util\data\unittest;

use lang\IllegalArgumentException;
use test\{Assert, Expect, Test, Values};
use util\data\{Enumeration, Sequence};

class EnumerationTest {
  use Enumerables;

  #[Test, Values(from: 'validArrays')]
  public function all_in_array($enumerable, $desc) {
    $result= [];
    foreach (Enumeration::of($enumerable) as $value) {
      $result[]= $value;
    }
    Assert::equals([1, 2, 3], $result, $desc);
  }

  #[Test, Values(from: 'validMaps')]
  public function all_in_map($enumerable, $desc) {
    $result= [];
    foreach (Enumeration::of($enumerable) as $key => $value) {
      $result[$key]= $value;
    }
    Assert::equals(['color' => 'green', 'price' => 12.99], $result, $desc);
  }

  #[Test]
  public function all_in_sequence() {
    $result= [];
    foreach (Enumeration::of(Sequence::of([1, 2, 3])) as $value) {
      $result[]= $value;
    }
    Assert::equals([1, 2, 3], $result);
  }

  #[Test]
  public function lazy_init_via_function() {
    $result= [];
    foreach (Enumeration::of(function() { return [1, 2, 3]; }) as $value) {
      $result[]= $value;
    }
    Assert::equals([1, 2, 3], $result);
  }

  #[Test, Values(from: 'invalid'), Expect(IllegalArgumentException::class)]
  public function raises_exception_when_given($value) {
    Enumeration::of($value);
  }

  #[Test]
  public function returns_empty_enumerable_for_null() {
    Assert::equals([], Enumeration::of(null));
  }

  #[Test, Expect(IllegalArgumentException::class)]
  public function returns_empty_enumerable_for_non_iterables() {
    Enumeration::of('a string');
  }
}