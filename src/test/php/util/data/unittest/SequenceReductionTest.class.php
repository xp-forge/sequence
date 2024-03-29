<?php namespace util\data\unittest;

use test\{Assert, Test, Values};
use util\Date;
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

  #[Test, Values([[[1, null, 2], 1], [[1, 2], 1], [[null, 1], 1], [[null], null], [[], null]])]
  public function used_for_first_nonnull_element($input, $expect) {
    Assert::equals($expect, Sequence::of($input)->reduce(null, function($a, $b) {
      return $a ?? $b;
    }));
  }

  #[Test]
  public function used_for_date_longest_ago() {
    $dates= [new Date('2021-11-23'), new Date('2021-10-02'), new Date('2021-12-14'), new Date('2021-02-10')];

    Assert::equals(new Date('2021-02-10'), Sequence::of($dates)->reduce(null, function($a, $b) {
      return null === $a || $b->isBefore($a) ? $b : $a;
    }));
  }

  #[Test]
  public function used_for_most_recent_date() {
    $dates= [new Date('2021-11-23'), new Date('2021-10-02'), new Date('2021-12-14'), new Date('2021-02-10')];

    Assert::equals(new Date('2021-12-14'), Sequence::of($dates)->reduce(null, function($a, $b) {
      return null === $a || $b->isAfter($a) ? $b : $a;
    }));
  }
}