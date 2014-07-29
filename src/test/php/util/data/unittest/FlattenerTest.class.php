<?php namespace util\data\unittest;

use util\data\Flattener;
use util\data\Sequence;

class FlattenerTest extends \unittest\TestCase {

  /**
   * Maps values with a given apply function using the CUT.
   *
   * @param  var[] $values
   * @return var[]
   */
  protected function flatten($values) {
    $result= [];
    foreach (new Flattener(new \ArrayIterator($values)) as $val) {
      $result[]= $val;
    }
    return $result;
  }

  #[@test]
  public function works_with_empty_input() {
    $this->assertEquals([], $this->flatten([]));
  }

  #[@test]
  public function flattens_arrays() {
    $this->assertEquals([1, 2, 3, 4], $this->flatten([[1, 2], [3, 4]]));
  }

  #[@test]
  public function flattens_array_and_sequence() {
    $this->assertEquals([1, 2, 3, 4], $this->flatten([[1, 2], Sequence::of([3, 4])]));
  }

  #[@test]
  public function flattens_handles_null_as_empty_sequence() {
    $this->assertEquals([1, 2, 3, 4], $this->flatten([null, null, [1], null, [2, 3, 4], null]));
  }

  #[@test]
  public function flatten_with_only_null_input() {
    $this->assertEquals([], $this->flatten([null, null, null]));
  }

  #[@test]
  public function flatten_with_empty_sequence_at_end() {
    $this->assertEquals([1, 2], $this->flatten([[1, 2], Sequence::$EMPTY]));
  }

  #[@test]
  public function flatten_with_empty_sequence_at_beginning() {
    $this->assertEquals([1, 2], $this->flatten([Sequence::$EMPTY, [1, 2]]));
  }
}