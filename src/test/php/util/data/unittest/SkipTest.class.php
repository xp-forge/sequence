<?php namespace util\data\unittest;

use util\data\Sequence;

class SkipTest extends AbstractSequenceTest {

  #[@test]
  public function excludes_n_first_elements() {
    $this->assertSequence([3, 4], Sequence::of([1, 2, 3, 4])->skip(2));
  }

  #[@test]
  public function excludes_elements_when_given_closure_returns_true() {
    $this->assertSequence([3, 4], Sequence::of([1, 2, 3, 4])->skip(function($e) { return $e < 3; }));
  }
}