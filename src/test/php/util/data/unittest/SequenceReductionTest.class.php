<?php namespace util\data\unittest;

use util\data\Sequence;

class SequenceReductionTest extends AbstractSequenceTest {

  #[@test]
  public function returns_identity_for_empty_input() {
    $this->assertEquals(-1, Sequence::of([])->reduce(-1, function($a, $b) {
      $this->fail('Should not be called');
    }));
  }

  #[@test]
  public function used_for_summing() {
    $this->assertEquals(10, Sequence::of([1, 2, 3, 4])->reduce(0, function($a, $b) {
      return $a + $b;
    }));
  }

  #[@test]
  public function used_for_max() {
    $this->assertEquals(10, Sequence::of([7, 1, 10, 3])->reduce(0, function($a, $b) {
      return $a < $b ? $b : $a;
    }));
  }

  #[@test]
  public function used_for_concatenation() {
    $this->assertEquals('Hello World', Sequence::of(['Hello', ' ', 'World'])->reduce('', function($a, $b) {
      return $a.$b;
    }));
  }
}