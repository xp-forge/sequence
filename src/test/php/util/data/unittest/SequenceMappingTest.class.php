<?php namespace util\data\unittest;

use util\data\Sequence;

class SequenceMappingTest extends AbstractSequenceTest {

  #[@test]
  public function with_function() {
    $this->assertSequence([2, 4, 6, 8], Sequence::of([1, 2, 3, 4])->map(function($e) { return $e * 2; }));
  }

  #[@test]
  public function with_with_floor_native_function() {
    $this->assertSequence([1.0, 2.0, 3.0], Sequence::of([1.9, 2.5, 3.1])->map('floor'));
  }

  #[@test, @values('noncallables'), @expect('lang.IllegalArgumentException')]
  public function map_raises_exception_when_given($noncallable) {
    Sequence::of([])->map($noncallable);
  }

  #[@test]
  public function array_index_is_passed_to_function() {
    $keys= [];
    Sequence::of([1, 2, 3])->map(function($e, $key) use(&$keys) { $keys[]= $key; return $e; })->each();
    $this->assertEquals([0, 1, 2], $keys);
  }

  #[@test]
  public function map_key_is_passed_to_function() {
    $keys= [];
    Sequence::of(['one' => 1, 'two' => 2, 'three' => 3])->map(function($e, $key) use(&$keys) { $keys[]= $key; return $e; })->each();
    $this->assertEquals(['one', 'two', 'three'], $keys);
  }
}