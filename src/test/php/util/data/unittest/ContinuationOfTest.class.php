<?php namespace util\data\unittest;

use test\{Assert, Test, Values};
use util\data\{ContinuationOf, Sequence};

class ContinuationOfTest {
  use Enumerables;

  #[Test, Values(from: 'validArrays')]
  public function at_beginning($input) {
    $it= Sequence::of($input)->getIterator();
    $it->rewind();

    Assert::equals([0 => 1, 1 => 2, 2 => 3], iterator_to_array(new ContinuationOf($it)));
  }

  #[Test, Values(from: 'validArrays')]
  public function after_first($input) {
    $it= Sequence::of($input)->getIterator();
    $it->rewind();
    $it->next();

    Assert::equals([1 => 2, 2 => 3], iterator_to_array(new ContinuationOf($it)));
  }

  #[Test, Values(from: 'validArrays')]
  public function at_end($input) {
    $it= Sequence::of($input)->getIterator();
    $it->rewind();
    $it->next();
    $it->next();
    $it->next();

    Assert::equals([], iterator_to_array(new ContinuationOf($it)));
  }
}