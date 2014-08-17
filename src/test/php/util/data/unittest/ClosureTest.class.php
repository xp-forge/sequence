<?php namespace util\data\unittest;

use util\data\Closure;

class ClosureTest extends \unittest\TestCase {
  protected $member= 'Member';
  protected static $static= 'Static';

  /** @return string */
  public static function staticFixture() {
    return self::$static;
  }

  /** @return string */
  public function memberFixture() {
    return $this->member;
  }

  #[@test]
  public function supports_closures() {
    $this->assertEquals('Test', Closure::of(function() { return 'Test'; })->__invoke());
  }

  #[@test]
  public function supports_string_reference_to_native_function() {
    $this->assertEquals(4, Closure::of('strlen')->__invoke('Test'));
  }

  #[@test]
  public function supports_string_reference_to_static_method_with_fqcn() {
    $this->assertEquals('Static', Closure::of('util.data.unittest.ClosureTest::staticFixture')->__invoke());
  }

  #[@test]
  public function supports_array_with_class_and_static_method() {
    $this->assertEquals('Static', Closure::of([__CLASS__, 'staticFixture'])->__invoke());
  }

  #[@test]
  public function supports_array_with_class_and_static_method_with_fqcn() {
    $this->assertEquals('Static', Closure::of([$this->getClassName(), 'staticFixture'])->__invoke());
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function raises_exception_when_class_in_array_does_not_exist() {
    Closure::of(['NonExistantClass', 'irrelevant']);
  }

  #[@test]
  public function supports_array_with_instance_and_method() {
    $this->assertEquals('Member', Closure::of([$this, 'memberFixture'])->__invoke());
  }

  #[@test, @expect('lang.IllegalArgumentException'), @values([
  #  [['util.data.unittest.ClosureTest', 'memberFixture']],
  #  ['util.data.unittest.ClosureTest::memberFixture'],
  #])]
  public function raises_exception_when_non_static_method_given_with_class($arg) {
    $this->assertEquals('Member', Closure::of($arg)->__invoke());
  }

  #[@test, @expect('lang.IllegalArgumentException'), @values([0.5, 1, true, null, [[]]])]
  public function raises_exception_when_first_element_in_array_is_neither_string_nor_object($value) {
    Closure::of([$value, 'irrelevant']);
  }

  #[@test, @expect('lang.IllegalArgumentException'), @values([0.5, 1, true, null, [[]], new Object()])]
  public function raises_exception_when_second_element_in_array_is_not_a_string($value) {
    Closure::of([__CLASS__, $value]);
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function raises_exception_when_neither_method_nor_call_handler_exist() {
    Closure::of([new \stdClass(), 'nonExistant']);
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function raises_exception_when_neither_method_nor_static_call_handler_exist() {
    Closure::of(['stdClass', 'nonExistant']);
  }

  #[@test, @expect('lang.IllegalArgumentException'), @values([
  #  [[]], [[1, 2]], [['', '']],
  #  [true], [false],
  #  [0], [-1], [0.5],
  #  [''],
  #  [null], [new \lang\Object()]
  #])]
  public function raises_exception_when_given_invalid($value) {
    Closure::of($value);
  }
}