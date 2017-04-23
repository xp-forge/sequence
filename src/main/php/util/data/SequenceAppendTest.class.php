<?php namespace util\data\unittest;

use util\data\Sequence;

class SequenceAppendTest extends AbstractSequenceTest {

  #[@test, @values('util.data.unittest.Enumerables::validArrays')]
  public function append_sequence_with($value) {
    $this->assertSequence([5, 6, 1, 2, 3], Sequence::of([5, 6])->append($value));
  }

  #[@test, @values('util.data.unittest.Enumerables::validArrays')]
  public function append_empty_with($value) {
    $this->assertSequence([1, 2, 3], Sequence::$EMPTY->append($value));
  }

  #[@test]
  public function append_null() {
    $this->assertSequence([1, 2, 3], Sequence::of([1, 2, 3])->append(null));
  }

  #[@test]
  public function append_empty() {
    $this->assertSequence([], Sequence::$EMPTY->append(null));
  }

  #[@test]
  public function append_iteratively() {
    $seq= Sequence::$EMPTY;
    foreach ([[1, 2], [3, 4], [5, 6]] as $array) {
      $seq= $seq->append($array);
    }
    $this->assertSequence([1, 2, 3, 4, 5, 6], $seq);
  }
}