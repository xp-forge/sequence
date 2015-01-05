<?php namespace util\data\unittest;

use util\data\Sequence;
use util\data\WorkerFunctions;
use util\data\WorkerProcesses;
use util\data\LocalWorkerProcess;
use lang\Runtime;

class SequenceDistributionTest extends AbstractSequenceTest {

  #[@test]
  public function one_function() {
    $processed= [0 => []];
    $workers= new WorkerFunctions([
      function($e) use(&$processed) { $processed[0][]= $e; }
    ]);
    Sequence::of([1, 2, 3, 4, 5, 6, 7, 8])->distribute($workers)->count();
    $this->assertEquals([0 => [1, 2, 3, 4, 5, 6, 7, 8]], $processed);
  }

  #[@test]
  public function two_functions() {
    $processed= [0 => [], 1 => []];
    $workers= new WorkerFunctions([
      function($e) use(&$processed) { $processed[0][]= $e; },
      function($e) use(&$processed) { $processed[1][]= $e; }
    ]);
    Sequence::of([1, 2, 3, 4, 5, 6, 7, 8])->distribute($workers)->count();
    $this->assertEquals([0 => [1, 3, 5, 7], 1 => [2, 4, 6, 8]], $processed);
  }

  #[@test]
  public function three_functions() {
    $processed= [0 => [], 1 => [], 2 => []];
    $workers= new WorkerFunctions([
      function($e) use(&$processed) { $processed[0][]= $e; },
      function($e) use(&$processed) { $processed[1][]= $e; },
      function($e) use(&$processed) { $processed[2][]= $e; }
    ]);
    Sequence::of([1, 2, 3, 4, 5, 6, 7, 8])->distribute($workers)->count();
    $this->assertEquals([0 => [1, 4, 7], 1 => [2, 5, 8], 2 => [3, 6]], $processed);
  }

  #[@test]
  public function processes() {
    $workers= [
      new LocalWorkerProcess('util.data.unittest.Worker', [3]),
      new LocalWorkerProcess('util.data.unittest.Worker', [10])
    ];

    $results= Sequence::of([1, 2, 3, 4, 5, 6, 7, 8])->distribute(new WorkerProcesses($workers))->toArray();

    foreach ($workers as $worker) {
      $worker->shutdown();
    }

    // The order in which results are returned cannot be guaranteed!
    sort($results);
    $this->assertEquals([3, 9, 15, 20, 21, 40, 60, 80], $results);
  }
}