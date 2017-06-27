<?php namespace util\data\unittest;

use util\data\Sequence;

class SequenceGroupingTest extends AbstractSequenceTest {

  #[@test]
  public function empty_in_groups_of_three() {
    $this->assertSequence([], Sequence::$EMPTY->grouped(3));
  }

  #[@test, @values([
  #  [[1]], [['one' => 1]],
  #  [[1, 2]], [['one' => 1, 'two' => 2]],
  #  [[1, 2, 3]], [['one' => 1, 'two' => 2, 'three' => 3]]
  #])]
  public function less_than_or_equal_to_three_in_groups_of_three($in) {
    $this->assertSequence([$in], Sequence::of($in)->grouped(3));
  }

  #[@test]
  public function six_in_groups_of_three() {
    $this->assertSequence([[1, 2, 3], [4, 5, 6]], Sequence::of([1, 2, 3, 4, 5, 6])->grouped(3));
  }

  #[@test]
  public function seven_in_groups_of_three() {
    $this->assertSequence([[1, 2, 3], [4, 5, 6], [7]], Sequence::of([1, 2, 3, 4, 5, 6, 7])->grouped(3));
  }

  #[@test]
  public function four_in_groups_of_three_with_keys() {
    $this->assertSequence(
      [['one' => 1, 'two' => 2, 'three' => 3], ['four' => 4]],
      Sequence::of(['one' => 1, 'two' => 2, 'three' => 3, 'four' => 4])->grouped(3)
    );
  }
}