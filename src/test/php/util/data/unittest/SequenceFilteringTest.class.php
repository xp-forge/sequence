<?php namespace util\data\unittest;

use lang\IllegalArgumentException;
use unittest\{Assert, Expect, Test, Values};
use util\Filter;
use util\data\Sequence;

class SequenceFilteringTest extends AbstractSequenceTest {

  #[Test]
  public function with_function() {
    $this->assertSequence([2, 4], Sequence::of([1, 2, 3, 4])->filter(function($e) { return 0 === $e % 2; }));
  }

  #[Test]
  public function with_is_string_native_function() {
    $this->assertSequence(['Hello', 'World'], Sequence::of(['Hello', 1337, 'World'])->filter('is_string'));
  }

  #[Test]
  public function with_filter_instance() {
    $this->assertSequence(['Hello', 'World'], Sequence::of(['Hello', '', 'World'])->filter(newinstance(Filter::class, [], [
      'accept' => function($e) { return strlen($e) > 0; }
    ])));
  }

  #[Test]
  public function with_generic_filter_instance() {
    $this->assertSequence(['Hello', 'World'], Sequence::of(['Hello', '', 'World'])->filter(newinstance('util.Filter<string>', [], [
      'accept' => function($e) { return strlen($e) > 0; }
    ])));
  }

  #[Test, Values('noncallables'), Expect(IllegalArgumentException::class)]
  public function raises_exception_when_given($noncallable) {
    Sequence::of([])->filter($noncallable);
  }

  #[Test]
  public function array_index_is_passed_to_function() {
    $keys= [];
    Sequence::of([1, 2, 3])->filter(function($e, $key) use(&$keys) { $keys[]= $key; return true; })->each();
    Assert::equals([0, 1, 2], $keys);
  }

  #[Test]
  public function map_key_is_passed_to_function() {
    $keys= [];
    Sequence::of(['one' => 1, 'two' => 2, 'three' => 3])->filter(function($e, $key) use(&$keys) { $keys[]= $key; return true; })->each();
    Assert::equals(['one', 'two', 'three'], $keys);
  }
}