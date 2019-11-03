<?php namespace util\data\unittest;

use unittest\Assert;
use util\data\Sequence;

class LimitTest extends AbstractSequenceTest {

  #[@test]
  public function stops_at_nth_array_element() {
    $this->assertSequence([1, 2], Sequence::of([1, 2, 3])->limit(2));
  }

  #[@test]
  public function stops_at_nth_iterator_element() {
    $this->assertSequence([1, 2], Sequence::iterate(1, function($i) { return ++$i; })->limit(2));
  }

  #[@test]
  public function stops_at_nth_generator_element() {
    $i= 1;
    $this->assertSequence([1, 2], Sequence::generate(function() use(&$i) { return $i++; })->limit(2));
  }

  #[@test]
  public function stops_when_given_closure_returns_true() {
    $this->assertSequence([1, 2], Sequence::of([1, 2, 3, 4])->limit(function($e) { return $e > 2; }));
  }

  #[@test]
  public function receives_offset() {
    $this->assertSequence([1, 2], Sequence::of([1, 2, 3, 4])->limit(function($e, $offset) { return $offset >= 2; }));
  }
}