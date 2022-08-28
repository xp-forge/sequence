<?php namespace util\data\unittest;

use lang\IllegalStateException;
use unittest\{Assert, Expect, Test, Values};
use util\Filter;
use util\data\{Optional, NoSuchElement};

class OptionalTest {

  /** @return iterable */
  private function emptyValues() {
    yield [null];
    yield [Optional::$EMPTY];
    yield [function() { return null; }];
    yield [function() { return Optional::$EMPTY; }];
  }

  #[Test]
  public function optional_created_via_of_is_present() {
    Assert::true(Optional::of('Test')->present());
  }

  #[Test]
  public function optional_created_via_of_with_null_is_not_present() {
    Assert::false(Optional::of(null)->present());
  }

  #[Test]
  public function empty_optional_is_not_present() {
    Assert::false(Optional::$EMPTY->present());
  }

  #[Test]
  public function get_returns_value_passed_to_of() {
    Assert::equals('Test', Optional::of('Test')->get());
  }

  #[Test, Expect(NoSuchElement::class)]
  public function get_throws_exception_when_no_value_is_present() {
    Optional::$EMPTY->get();
  }

  #[Test]
  public function orElse_returns_value_passed_to_of() {
    Assert::equals('Test', Optional::of('Test')->orElse('Failed'));
  }

  #[Test]
  public function orElse_returns_default_when_no_value_is_present() {
    Assert::equals('Succeeded', Optional::$EMPTY->orElse('Succeeded'));
  }

  #[Test]
  public function orUse_returns_value_passed_to_of() {
    Assert::equals('Test', Optional::of('Test')->orUse(function() {
      throw new IllegalStateException('Not reached');
    }));
  }

  #[Test]
  public function orUse_invokes_supplier_when_no_value_is_present() {
    Assert::equals('Succeeded', Optional::$EMPTY->orUse(function() { return 'Succeeded'; }));
  }

  #[Test]
  public function whenAbsent_returns_value_passed_to_of() {
    Assert::equals('Test', Optional::of('Test')->whenAbsent('Failed')->get());
  }

  #[Test]
  public function whenAbsent_returns_value_when_no_value_is_present() {
    Assert::equals('Succeeded', Optional::$EMPTY->whenAbsent('Succeeded')->get());
  }

  #[Test]
  public function whenAbsent_returns_optionals_value_when_no_value_is_present() {
    Assert::equals('Succeeded', Optional::$EMPTY->whenAbsent(Optional::of('Succeeded'))->get());
  }

  #[Test]
  public function whenAbsent_returns_functions_return_value_when_no_value_is_present() {
    Assert::equals('Succeeded', Optional::$EMPTY->whenAbsent(function() { return 'Succeeded'; })->get());
  }

  #[Test, Values('emptyValues')]
  public function whenAbsent_chaining($value) {
    Assert::equals('Succeeded', Optional::$EMPTY->whenAbsent($value)->whenAbsent('Succeeded')->get());
  }

  #[Test]
  public function can_be_used_in_foreach() {
    Assert::equals(['Test'], iterator_to_array(Optional::of('Test')));
  }

  #[Test]
  public function empty_can_be_used_in_foreach() {
    Assert::equals([], iterator_to_array(Optional::$EMPTY));
  }

  #[Test]
  public function filter_returns_empty_when_no_value_present() {
    Assert::equals(Optional::$EMPTY, Optional::$EMPTY->filter('is_array'));
  }

  #[Test]
  public function filter_returns_self_when_predicate_matches() {
    Assert::equals([1, 2, 3], Optional::of([1, 2, 3])->filter('is_array')->get());
  }

  #[Test]
  public function filter_returns_empty_when_predicate_does_not_match() {
    Assert::equals(Optional::$EMPTY, Optional::of('test')->filter('is_array'));
  }

  #[Test]
  public function filter_returns_optional_instance_with_null() {
    $optional= Optional::of('test');
    Assert::true($optional === $optional->filter(null));
  }

  #[Test]
  public function filter_with_filter_instance() {
    $filter= new class() implements Filter {
      public function accept($value) { return preg_match('/^www/', $value); }
    };
    Assert::equals('www.example.com', Optional::of('www.example.com')->filter($filter)->get());
  }

  #[Test]
  public function map_applies_function_when_value_present() {
    Assert::equals('123', Optional::of([1, 2, 3])->map('implode')->get());
  }

  #[Test]
  public function map_returns_empty_when_no_value_present() {
    Assert::equals(Optional::$EMPTY, Optional::$EMPTY->map('implode'));
  }

  #[Test]
  public function map_returns_optional_instance_with_null() {
    $optional= Optional::of('test');
    Assert::true($optional === $optional->map(null));
  }

  #[Test]
  public function toString_for_empty_optional() {
    Assert::equals('util.data.Optional<EMPTY>', Optional::$EMPTY->toString());
  }

  #[Test]
  public function toString_for_sequence_of_array() {
    Assert::equals('util.data.Optional@[1, 2, 3]', Optional::of([1, 2, 3])->toString());
  }
}