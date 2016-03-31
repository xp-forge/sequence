<?php namespace util\data\unittest;

use util\data\Sequence;
use util\data\Calculations;

class CalculationsTest extends \unittest\TestCase {

  #[@test]
  public function min_max_and_average() {
    Sequence::of([1, 2, 3, 4])
      ->collecting(Calculations::min(), $min)
      ->collecting(Calculations::max(), $max)
      ->collecting(Calculations::average(), $average)
      ->each()
    ;    
    $this->assertEquals(1, $min);
    $this->assertEquals(4, $max);
    $this->assertEquals(2.5, $average);
  }
}