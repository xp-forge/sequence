<?php namespace util\data\unittest;

use util\data\Sequence;
use util\data\Optional;
use lang\IllegalArgumentException;

class SequenceFlatteningTest extends AbstractSequenceTest {

  #[@test]
  public function flatten_without_mapper() {
    $this->assertSequence(['a', 'b', 'c', 'd'], Sequence::of([['a', 'b'], ['c', 'd']])->flatten());
  }

  #[@test]
  public function flatten_with_mapper() {
    $this->assertSequence(['a', 'b', 'c', 'd'], Sequence::of(['a', 'c'])->flatten(function($e) {
      return Sequence::iterate($e, function($n) { return ++$n; })->limit(2);
    }));
  }

  #[@test]
  public function flatten_optionals() {
    $this->assertSequence(['a', 'b'], Sequence::of([Optional::of('a'), Optional::$EMPTY, Optional::of('b')])->flatten());
  }

  #[@test, @values('noncallables'), @expect('lang.IllegalArgumentException')]
  public function flatten_raises_exception_when_given($noncallable) {
    if (null === $noncallable) {
      throw new IllegalArgumentException('Valid use-case');
    }
    Sequence::of([])->flatten($noncallable);
  }
}