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
}