<?php namespace util\data\unittest;

use unittest\{Assert, Test};
use util\data\Sequence;

class SequenceReductionTest extends AbstractSequenceTest {

  #[Test]
  public function returns_identity_for_empty_input() {
    Assert::equals(-1, Sequence::of([])->reduce(-1, function($a, $b) {
      $this->fail('Should not be called');
    }));
  }

  #[Test]
  public function used_for_summing() {
    Assert::equals(10, Sequence::of([1, 2, 3, 4])->reduce(0, function($a, $b) {
      return $a + $b;
    }));
  }

  #[Test]
  public function used_for_max() {
    Assert::equals(10, Sequence::of([7, 1, 10, 3])->reduce(0, function($a, $b) {
      return $a < $b ? $b : $a;
    }));
  }

  #[Test]
  public function used_for_concatenation() {
    Assert::equals('Hello World', Sequence::of(['Hello', ' ', 'World'])->reduce('', function($a, $b) {
      return $a.$b;
    }));
  }
}