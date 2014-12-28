<?php namespace util\data\unittest;

use util\data\Sequence;

class SequenceSlidingTest extends AbstractSequenceTest {

  #[@test]
  public function empty_in_slides_of_three() {
    $this->assertSequence([], Sequence::$EMPTY->sliding(3));
  }

  #[@test, @values([
  #  [[1]],
  #  [[1, 2]],
  #  [[1, 2, 3]]
  #])]
  public function less_than_or_equal_to_three_in_slides_of_three($value) {
    $this->assertSequence([$value], Sequence::of($value)->sliding(3));
  }

  #[@test]
  public function three_in_slides_of_three() {
    $this->assertSequence([[1, 2, 3]], Sequence::of([1, 2, 3])->sliding(3));
  }

  #[@test]
  public function four_in_slides_of_three() {
    $this->assertSequence([[1, 2, 3], [2, 3, 4]], Sequence::of([1, 2, 3, 4])->sliding(3));
  }

  #[@test]
  public function six_in_slides_of_three() {
    $this->assertSequence([[1, 2, 3], [2, 3, 4], [3, 4, 5], [4, 5, 6]], Sequence::of([1, 2, 3, 4, 5, 6])->sliding(3));
  }
}
