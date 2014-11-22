<?php namespace util\data\unittest;

use util\data\Sequence;

class DistributionTest extends AbstractSequenceTest {

  #[@test]
  public function one() {
    $processed= [0 => []];
    $workers= [
      function($e) use(&$processed) { $processed[0][]= $e; }
    ];
    Sequence::of([1, 2, 3, 4, 5, 6, 7, 8])->distribute($workers)->count();
    $this->assertEquals([0 => [1, 2, 3, 4, 5, 6, 7, 8]], $processed);
  }

  #[@test]
  public function two() {
    $processed= [0 => [], 1 => []];
    $workers= [
      function($e) use(&$processed) { $processed[0][]= $e; },
      function($e) use(&$processed) { $processed[1][]= $e; }
    ];
    Sequence::of([1, 2, 3, 4, 5, 6, 7, 8])->distribute($workers)->count();
    $this->assertEquals([0 => [1, 3, 5, 7], 1 => [2, 4, 6, 8]], $processed);
  }

  #[@test]
  public function three() {
    $processed= [0 => [], 1 => [], 2 => []];
    $workers= [
      function($e) use(&$processed) { $processed[0][]= $e; },
      function($e) use(&$processed) { $processed[1][]= $e; },
      function($e) use(&$processed) { $processed[2][]= $e; }
    ];
    Sequence::of([1, 2, 3, 4, 5, 6, 7, 8])->distribute($workers)->count();
    $this->assertEquals([0 => [1, 4, 7], 1 => [2, 5, 8], 2 => [3, 6]], $processed);
  }
}