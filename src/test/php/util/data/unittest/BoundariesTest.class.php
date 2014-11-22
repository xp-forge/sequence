<?php namespace util\data\unittest;

use util\data\Sequence;
use util\Date;

class BoundariesTest extends AbstractSequenceTest {

  #[@test, @values([
  #  [null, []],
  #  [1, [1]],
  #  [2, [10, 7, 2]]
  #])]
  public function min($result, $values) {
    $this->assertEquals($result, Sequence::of($values)->min());
  }

  #[@test]
  public function min_using_comparator() {
    $this->assertEquals(
      new Date('1977-12-14'),
      Sequence::of([new Date('1977-12-14'), new Date('2014-07-17'), new Date('1979-12-29')])->min(newinstance('util.Comparator', [], [
        'compare' => function($a, $b) { return $b->compareTo($a); }
      ]))
    );
  }

  #[@test]
  public function min_using_closure() {
    $this->assertEquals(
      new Date('1977-12-14'),
      Sequence::of([new Date('1977-12-14'), new Date('2014-07-17'), new Date('1979-12-29')])->min(function($a, $b) {
        return $b->compareTo($a);
      })
    );
  }

  #[@test, @values([
  #  [null, []],
  #  [1, [1]],
  #  [10, [2, 10, 7]]
  #])]
  public function max($result, $values) {
    $this->assertEquals($result, Sequence::of($values)->max());
  }

  #[@test]
  public function max_using_comparator() {
    $this->assertEquals(
      new Date('2014-07-17'),
      Sequence::of([new Date('1977-12-14'), new Date('2014-07-17'), new Date('1979-12-29')])->max(newinstance('util.Comparator', [], [
        'compare' => function($a, $b) { return $b->compareTo($a); }
      ]))
    );
  }

  #[@test]
  public function max_using_closure() {
    $this->assertEquals(
      new Date('2014-07-17'),
      Sequence::of([new Date('1977-12-14'), new Date('2014-07-17'), new Date('1979-12-29')])->max(function($a, $b) {
        return $b->compareTo($a);
      })
    );
  }
}