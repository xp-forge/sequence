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

  #[@test, @values('noncallables'), @expect(IllegalArgumentException::class)]
  public function flatten_raises_exception_when_given($noncallable) {
    if (null === $noncallable) {
      throw new IllegalArgumentException('Valid use-case');
    }
    Sequence::of([])->flatten($noncallable);
  }

  #[@test]
  public function array_index_is_passed_to_function() {
    $keys= [];
    Sequence::of([['a', 'b'], ['c', 'd']])->flatten(function($e, $key) use(&$keys) { $keys[]= $key; return $e; })->each();
    $this->assertEquals([0, 1], $keys);
  }

  #[@test]
  public function map_key_is_passed_to_function() {
    $keys= [];
    Sequence::of(['one' => [1], 'two' => [2], 'three' => [3]])->flatten(function($e, $key) use(&$keys) { $keys[]= $key; return $e; })->each();
    $this->assertEquals(['one', 'two', 'three'], $keys);
  }
}