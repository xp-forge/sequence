<?php namespace util\data\unittest;

use lang\IllegalArgumentException;
use unittest\{Assert, Expect, Test, Values};
use util\data\{Optional, Sequence};

class SequenceFlatteningTest extends AbstractSequenceTest {

  #[Test]
  public function flatten_without_mapper() {
    $this->assertSequence(['a', 'b', 'c', 'd'], Sequence::of([['a', 'b'], ['c', 'd']])->flatten());
  }

  #[Test]
  public function flatten_with_mapper() {
    $this->assertSequence(['a', 'b', 'c', 'd'], Sequence::of(['a', 'c'])->flatten(function($e) {
      return Sequence::iterate($e, function($n) { return ++$n; })->limit(2);
    }));
  }

  #[Test]
  public function flatten_optionals() {
    $this->assertSequence(['a', 'b'], Sequence::of([Optional::of('a'), Optional::$EMPTY, Optional::of('b')])->flatten());
  }

  #[Test, Values('noncallables'), Expect(IllegalArgumentException::class)]
  public function flatten_raises_exception_when_given($noncallable) {
    if (null === $noncallable) {
      throw new IllegalArgumentException('Valid use-case');
    }
    Sequence::of([])->flatten($noncallable);
  }

  #[Test]
  public function array_index_is_passed_to_function() {
    $keys= [];
    Sequence::of([['a', 'b'], ['c', 'd']])->flatten(function($e, $key) use(&$keys) { $keys[]= $key; return $e; })->each();
    Assert::equals([0, 1], $keys);
  }

  #[Test]
  public function map_key_is_passed_to_function() {
    $keys= [];
    Sequence::of(['one' => [1], 'two' => [2], 'three' => [3]])->flatten(function($e, $key) use(&$keys) { $keys[]= $key; return $e; })->each();
    Assert::equals(['one', 'two', 'three'], $keys);
  }

  #[Test]
  public function flatten_generator() {
    $this->assertSequence([2, 4, 0, 4, 1], Sequence::of([2])->flatten(function($n) {
      yield $n;
      yield $n + $n;
      yield $n - $n;
      yield $n * $n;
      yield $n / $n;
    }));
  }

  #[Test]
  public function flatten_generator_with_key() {
    $this->assertSequence([3, 6], Sequence::of([1 => 2])->flatten(function($n, $key) {
      yield $n + $key;
      yield $n * ($n + $key);
    }));
  }
}