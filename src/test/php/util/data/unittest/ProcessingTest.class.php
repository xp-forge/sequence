<?php namespace util\data\unittest;

use util\data\Sequence;
use util\data\Processing;
use lang\IndexOutOfBoundsException;

class ProcessingTest extends \unittest\TestCase {

  #[@test]
  public function defer() {
    $result= Sequence::of([1, 2, 3, 4])
      ->process(function($processing, $i) {
        if (0 === $i % 2) $processing->defer($i);
      })
      ->map(function($i) { return 2 * $i; })
    ;

    $this->assertEquals([2, 6, 4, 8], $result->toArray());
  }

  #[@test]
  public function drop() {
    $result= Sequence::of([1, 2, 3, 4])
      ->process(function($processing, $i) {
        if (0 === $i % 2) $processing->drop($i);
      })
      ->map(function($i) { return 2 * $i; })
    ;

    $this->assertEquals([2, 6], $result->toArray());
  }

  #[@test]
  public function retry() {
    $result= Sequence::of(['hello', '  hi'])
      ->process(function($processing, $index) {
        static $map= ['hello' => 1, 'hi' => 2];

        try {
          return $map[$index];
        } catch (IndexOutOfBoundsException $t) {
          $processing->retry(substr($index, 1));
        }
      })
      ->map(function($i) { return 2 * $i; })
    ;

    $this->assertEquals([2, 4], $result->toArray());
  }
}