<?php namespace util\data\unittest;

use util\data\Optional;

class OptionalTest extends \unittest\TestCase {

  #[@test]
  public function optional_created_via_of_is_present() {
    $this->assertTrue(Optional::of('Test')->present());
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
  public function can_be_used_in_foreach() {
    $this->assertEquals(['Test'], iterator_to_array(Optional::of('Test')));
  }

  #[@test]
  public function empty_can_be_used_in_foreach() {
    $this->assertEquals([], iterator_to_array(Optional::$EMPTY));
  }
}