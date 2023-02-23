<?php namespace util\data\unittest;

use lang\IllegalArgumentException;
use test\{Assert, Expect, Test, Values};
use util\data\Sequence;

class SequenceWindowTest extends AbstractSequenceTest {

  #[Test]
  public function chunked() {
    $this->assertSequence(
      [[1, 2], [3, 4]],
      Sequence::of([1, 2, 3, 4])->chunked(2)
    );
  }

  #[Test]
  public function chunked_with_partial() {
    $this->assertSequence(
      [[1, 2], [3, 4], [5]],
      Sequence::of([1, 2, 3, 4, 5])->chunked(2)
    );
  }

  #[Test]
  public function sliding_window() {
    $this->assertSequence(
      [[1, 2], [2, 3], [3, 4]],
      Sequence::of([1, 2, 3, 4])->windowed(2)
    );
  }

  #[Test]
  public function window_size_equal_to_step() {
    $this->assertSequence(
      [[1, 2], [3, 4]],
      Sequence::of([1, 2, 3, 4])->windowed(2, 2)
    );
  }

  #[Test]
  public function skip_elements() {
    $this->assertSequence(
      [[1, 2], [4, 5]],
      Sequence::of([1, 2, 3, 4, 5])->windowed(2, 3)
    );
  }

  #[Test, Values([[true, [[1, 2], [3, 4], [5]]], [false, [[1, 2], [3, 4]]]])]
  public function partial_window($flag, $expected) {
    $this->assertSequence(
      $expected,
      Sequence::of([1, 2, 3, 4, 5])->windowed(2, 2, $flag)
    );
  }

  #[Test]
  public function partial_window_multiple_partials_at_end() {
    $this->assertSequence(
      [[1, 2, 3, 4, 5], [4, 5, 6, 7, 8], [7, 8, 9, 10], [10]],
      Sequence::of([1, 2, 3, 4, 5, 6, 7, 8, 9, 10])->windowed(5, 3, true)
    );
  }

  #[Test]
  public function skip_elements_and_return_partial() {
    $this->assertSequence(
      [[1, 2], [5]],
      Sequence::of([1, 2, 3, 4, 5])->windowed(2, 4, true)
    );
  }

  #[Test, Values([[[], []], [[1], [[1]]]])]
  public function chunked_edge_cases($elements, $expected) {
    $this->assertSequence($expected, Sequence::of($elements)->chunked(2));
  }

  #[Test, Values([[[], []], [[1], []]])]
  public function windowed_edge_cases_without_partial($elements, $expected) {
    $this->assertSequence($expected, Sequence::of($elements)->windowed(2, 1, false));
  }

  #[Test, Values([[[], []], [[1], [[1]]]])]
  public function windowed_edge_cases_with_partial($elements, $expected) {
    $this->assertSequence($expected, Sequence::of($elements)->windowed(2, 1, true));
  }

  #[Test, Expect(IllegalArgumentException::class), Values([-1, 0])]
  public function chunk_size_must_greater_than_zero($size) {
    Sequence::of([1])->chunked($size);
  }

  #[Test, Expect(IllegalArgumentException::class), Values([-1, 0])]
  public function window_size_must_greater_than_zero($size) {
    Sequence::of([1])->windowed($size);
  }

  #[Test, Expect(IllegalArgumentException::class), Values([-1, 0])]
  public function window_step_must_greater_than_zero($step) {
    Sequence::of([1])->windowed(1, $step);
  }
}