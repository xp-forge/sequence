<?php namespace util\data\unittest;

use util\data\Sequence;
use lang\IllegalStateException;

class CatchTest extends \unittest\TestCase {

  #[@test, @expect(IllegalStateException::class)]
  public function without_catch() {
    $fixture= Sequence::of([1, 2, 3])
      ->peek(function($i) { if (0 === $i % 2) throw new IllegalStateException('Only odd numbers expected'); })
    ;
    $fixture->toArray();
  }

  #[@test]
  public function catch_nothing() {
    $fixture= Sequence::of([1, 2, 3])
      ->catch(function($e) { throw new IllegalStateException('Should not be reached'); })
    ;
    $this->assertEquals([1, 2, 3], $fixture->toArray());
  }

  #[@test]
  public function catch_exception_from_peek() {
    $fixture= Sequence::of([1, 2, 3])
      ->peek(function($i) { if (0 === $i % 2) throw new IllegalStateException('Only odd numbers expected'); })
      ->catch(function($e) { yield -1; })
    ;
    $this->assertEquals([1, -1, 3], $fixture->toArray());
  }

  #[@test]
  public function catch_exception_from_map() {
    $fixture= Sequence::of([1, 2, 3])
      ->map(function($i) { if (0 === $i % 2) throw new IllegalStateException('Only odd numbers expected'); return $i; })
      ->catch(function($e) { yield -1; })
    ;
    $this->assertEquals([1, -1, 3], $fixture->toArray());
  }

  #[@test]
  public function catch_exception_from_filter() {
    $fixture= Sequence::of([1, 2, 3])
      ->filter(function($i) { if (0 === $i % 2) throw new IllegalStateException('Only odd numbers expected'); return true; })
      ->catch(function($e) { yield -1; })
    ;
    $this->assertEquals([1, -1, 3], $fixture->toArray());
  }
}