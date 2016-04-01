<?php namespace util\data\unittest;

use util\data\Sequence;
use util\data\Calculations;

class CalculationsTest extends \unittest\TestCase {

  #[@test]
  public function min_max_sum_average_and_count_for_empty_sequence() {
    Sequence::$EMPTY
      ->collecting(Calculations::min(), $min)
      ->collecting(Calculations::max(), $max)
      ->collecting(Calculations::average(), $average)
      ->collecting(Calculations::sum(), $sum)
      ->collecting(Calculations::count(), $count)
      ->each()
    ;
    $this->assertNull($min);
    $this->assertNull($max);
    $this->assertNull($average);
    $this->assertEquals(0, $sum);
    $this->assertEquals(0, $count);
  }

  #[@test]
  public function min_max_sum_average_and_count_for_non_empty() {
    Sequence::of([1, 2, 3, 4])
      ->collecting(Calculations::min(), $min)
      ->collecting(Calculations::max(), $max)
      ->collecting(Calculations::average(), $average)
      ->collecting(Calculations::sum(), $sum)
      ->collecting(Calculations::count(), $count)
      ->each()
    ;
    $this->assertEquals(1, $min);
    $this->assertEquals(4, $max);
    $this->assertEquals(2.5, $average);
    $this->assertEquals(10, $sum);
    $this->assertEquals(4, $count);
  }
}