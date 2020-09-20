<?php namespace util\data\unittest;

use unittest\Assert;
use util\data\Sequence;
use util\{Comparator, Date};

class BoundariesTest extends AbstractSequenceTest {

  /** @return iterable */
  private function values() {
    yield [null, null, []];
    yield [1, 1, [1]];
    yield [2, 10, [10, 7, 2]];
  }

  /** @return iterable */
  private function comparators() {
    yield [newinstance(Comparator::class, [], ['compare' => function($a, $b) { return $b->compareTo($a); }])];
    yield [function($a, $b) { return $b->compareTo($a); }];
  }

  #[@test, @values('values')]
  public function min($min, $max, $values) {
    Assert::equals($min, Sequence::of($values)->min());
  }

  #[@test, @values('comparators')]
  public function min_using($comparator) {
    Assert::equals(
      new Date('1977-12-14'),
      Sequence::of([new Date('1977-12-14'), new Date('2014-07-17'), new Date('1979-12-29')])->min($comparator)
    );
  }

  #[@test, @values('values')]
  public function max($min, $max, $values) {
    Assert::equals($max, Sequence::of($values)->max());
  }

  #[@test, @values('comparators')]
  public function max_using($comparator) {
    Assert::equals(
      new Date('2014-07-17'),
      Sequence::of([new Date('1977-12-14'), new Date('2014-07-17'), new Date('1979-12-29')])->max($comparator)
    );
  }
}