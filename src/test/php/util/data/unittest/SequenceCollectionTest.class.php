<?php namespace util\data\unittest;

use test\{Assert, Test};
use util\data\{Collector, Sequence};

class SequenceCollectionTest extends AbstractSequenceTest {

  #[Test]
  public function used_for_averaging() {
    $result= Sequence::of([1, 2, 3, 4])->collect(new Collector(
      function() { return ['total' => 0, 'sum' => 0]; },
      function(&$result, $arg) { $result['total']++; $result['sum']+= $arg; }
    ));
    Assert::equals(2.5, $result['sum'] / $result['total']);
  }

  #[Test]
  public function used_for_joining() {
    $result= Sequence::of(['a', 'b', 'c'])->collect(new Collector(
      function() { return ''; },
      function(&$result, $arg) { $result.= ', '.$arg; },
      function($result) { return substr($result, 2); }
    ));
    Assert::equals('a, b, c', $result);
  }
}