<?php namespace util\data\unittest;

use util\data\Optional;
use lang\IllegalStateException;

class OptionalTest extends \unittest\TestCase {

  #[@test]
  public function optional_created_via_of_is_present() {
    $this->assertTrue(Optional::of('Test')->present());
  }

  #[@test]
  public function optional_created_via_of_with_null_is_not_present() {
    $this->assertFalse(Optional::of(null)->present());
  }

  #[@test]
  public function empty_optional_is_not_present() {
    $this->assertFalse(Optional::$EMPTY->present());
  }

  #[@test]
  public function get_returns_value_passed_to_of() {
    $this->assertEquals('Test', Optional::of('Test')->get());
  }

  #[@test, @expect('util.NoSuchElementException')]
  public function get_throws_exception_when_no_value_is_present() {
    Optional::$EMPTY->get();
  }

  #[@test]
  public function orElse_returns_value_passed_to_of() {
    $this->assertEquals('Test', Optional::of('Test')->orElse('Failed'));
  }

  #[@test]
  public function orElse_returns_default_when_no_value_is_present() {
    $this->assertEquals('Succeeded', Optional::$EMPTY->orElse('Succeeded'));
  }

  #[@test]
  public function orUse_returns_value_passed_to_of() {
    $this->assertEquals('Test', Optional::of('Test')->orUse(function() {
      throw new IllegalStateException('Not reached');
    }));
  }

  #[@test]
  public function orUse_invokes_supplier_when_no_value_is_present() {
    $this->assertEquals('Succeeded', Optional::$EMPTY->orUse(function() { return 'Succeeded'; }));
  }

  #[@test]
  public function whenAbsent_returns_value_passed_to_of() {
    $this->assertEquals('Test', Optional::of('Test')->whenAbsent('Failed')->get());
  }

  #[@test]
  public function whenAbsent_returns_value_when_no_value_is_present() {
    $this->assertEquals('Succeeded', Optional::$EMPTY->whenAbsent('Succeeded')->get());
  }

  #[@test]
  public function whenAbsent_returns_optionals_value_when_no_value_is_present() {
    $this->assertEquals('Succeeded', Optional::$EMPTY->whenAbsent(Optional::of('Succeeded'))->get());
  }

  #[@test]
  public function whenAbsent_returns_functions_return_value_when_no_value_is_present() {
    $this->assertEquals('Succeeded', Optional::$EMPTY->whenAbsent(function() { return 'Succeeded'; })->get());
  }

  #[@test, @values([
  #  [null],
  #  [Optional::$EMPTY],
  #  [function() { return null; }],
  #  [function() { return \util\data\Optional::$EMPTY; }]
  #])]
  public function whenAbsent_chaining($value) {
    $this->assertEquals('Succeeded', Optional::$EMPTY->whenAbsent($value)->whenAbsent('Succeeded')->get());
  }

  #[@test]
  public function can_be_used_in_foreach() {
    $this->assertEquals(['Test'], iterator_to_array(Optional::of('Test')));
  }

  #[@test]
  public function empty_can_be_used_in_foreach() {
    $this->assertEquals([], iterator_to_array(Optional::$EMPTY));
  }

  #[@test]
  public function filter_returns_empty_when_no_value_present() {
    $this->assertEquals(Optional::$EMPTY, Optional::$EMPTY->filter('is_array'));
  }

  #[@test]
  public function filter_returns_self_when_predicate_matches() {
    $this->assertEquals([1, 2, 3], Optional::of([1, 2, 3])->filter('is_array')->get());
  }

  #[@test]
  public function filter_returns_empty_when_predicate_does_not_match() {
    $this->assertEquals(Optional::$EMPTY, Optional::of('test')->filter('is_array'));
  }

  #[@test]
  public function map_applies_function_when_value_present() {
    $this->assertEquals('123', Optional::of([1, 2, 3])->map('implode')->get());
  }

  #[@test]
  public function map_returns_empty_when_no_value_present() {
    $this->assertEquals(Optional::$EMPTY, Optional::$EMPTY->map('implode'));
  }

  #[@test]
  public function toString_for_empty_optional() {
    $this->assertEquals('util.data.Optional<EMPTY>', Optional::$EMPTY->toString());
  }

  #[@test]
  public function toString_for_sequence_of_array() {
    $this->assertEquals('util.data.Optional@[1, 2, 3]', Optional::of([1, 2, 3])->toString());
  }
}