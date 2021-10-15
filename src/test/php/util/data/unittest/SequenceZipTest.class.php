<?php namespace util\data\unittest;

use unittest\{Assert, Test};
use util\data\Sequence;

class SequenceZipTest extends AbstractSequenceTest {

  #[Test]
  public function basic_functionality() {
    $result= Sequence::of([1, 2, 3, 4])->zip(['a', 'b', 'c', 'd'])->toArray();
    Assert::equals([[1, 'a'], [2, 'b'], [3, 'c'], [4, 'd']], $result);
  }

  #[Test]
  public function transform_using_function() {
    $result= Sequence::of([1, 2, 3, 4])->zip([1, 0, -1, 6], function($a, $b) { return $a - $b; })->toArray();
    Assert::equals([0, 2, 4, -2], $result);
  }

  #[Test]
  public function create_map_with_generator() {
    $result= Sequence::of([1, 2, 3, 4])->zip(['a', 'b', 'c', 'd'], function($a, $b) { yield $a => $b; })->toMap();
    Assert::equals([1 => 'a', 2 => 'b', 3 => 'c', 4 => 'd'], $result);
  }

  #[Test]
  public function shorter_sequence() {
    $result= Sequence::of([1, 2, 3])->zip(['a', 'b', 'c', 'd'])->toArray();
    Assert::equals([[1, 'a'], [2, 'b'], [3, 'c']], $result);
  }

  #[Test]
  public function shorter_argument() {
    $result= Sequence::of([1, 2, 3, 4])->zip(['a', 'b', 'c'])->toArray();
    Assert::equals([[1, 'a'], [2, 'b'], [3, 'c']], $result);
  }
}