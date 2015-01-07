<?php namespace util\data\unittest;

use util\data\Sequence;

class SequenceConcatTest extends AbstractSequenceTest {

  #[@test, @values('util.data.unittest.Enumerables::validArrays')]
  public function concat_sequence_with($value) {
    $this->assertSequence([5, 6, 1, 2, 3], Sequence::concat(Sequence::of([5, 6]), $value));
  }

  #[@test, @values('util.data.unittest.Enumerables::validArrays')]
  public function concat_array_with($value) {
    $this->assertSequence([5, 6, 1, 2, 3], Sequence::concat([5, 6], $value));
  }

  #[@test, @values('util.data.unittest.Enumerables::validArrays')]
  public function concat_empty_with($value) {
    $this->assertSequence([1, 2, 3], Sequence::concat(Sequence::$EMPTY, $value));
  }

  #[@test, @values('util.data.unittest.Enumerables::validArrays')]
  public function concat_one_arg($value) {
    $this->assertSequence([1, 2, 3], Sequence::concat($value));
  }

  #[@test]
  public function concat_null() {
    $this->assertSequence([], Sequence::concat(null, null));
  }

  #[@test]
  public function concat_empty() {
    $this->assertSequence([], Sequence::concat(Sequence::$EMPTY, Sequence::$EMPTY));
  }

  #[@test]
  public function concat_no_args() {
    $this->assertSequence([], Sequence::concat());
  }

  #[@test]
  public function concat_iteratively() {
    $seq= Sequence::$EMPTY;
    foreach ([[1, 2], [3, 4], [5, 6]] as $array) {
      $seq= Sequence::concat($seq, Sequence::of($array));
    }
    $this->assertSequence([1, 2, 3, 4, 5, 6], $seq);
  }
}