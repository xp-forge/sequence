<?php namespace util\data\unittest;

use util\data\Sequence;
use util\data\Calculations;

class CalculationsTest extends \unittest\TestCase {

  #[@test]
  public function min_max_average_and_count_for_empty_sequence() {
    Sequence::of([])
      ->collecting(Calculations::min(), $min)
      ->collecting(Calculations::max(), $max)
      ->collecting(Calculations::average(), $average)
      ->collecting(Calculations::count(), $count)
      ->each()
    ;
    $this->assertNull($min);
    $this->assertNull($max);
    $this->assertNull($average);
    $this->assertEquals(0, $count);
  }

  #[@test]
  public function min_max_average_and_count_for_non_empty() {
    Sequence::of([1, 2, 3, 4])
      ->collecting(Calculations::min(), $min)
      ->collecting(Calculations::max(), $max)
      ->collecting(Calculations::average(), $average)
      ->collecting(Calculations::count(), $count)
      ->each()
    ;
    $this->assertEquals(1, $min);
    $this->assertEquals(4, $max);
    $this->assertEquals(2.5, $average);
    $this->assertEquals(4, $count);
  }
}