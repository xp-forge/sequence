<?php namespace util\data\unittest;

use util\data\Sequence;
use util\data\Aggregations;

class AggregationsTest extends \unittest\TestCase {

  #[@test]
  public function min_max_sum_average_and_count_for_empty_sequence() {
    Sequence::$EMPTY
      ->collecting($min, Aggregations::min())
      ->collecting($max, Aggregations::max())
      ->collecting($average, Aggregations::average())
      ->collecting($sum, Aggregations::sum())
      ->collecting($count, Aggregations::count())
      ->each()
    ;
    $this->assertNull($min);
    $this->assertNull($max);
    $this->assertNull($average);
    $this->assertNull($sum);
    $this->assertEquals(0, $count);
  }

  #[@test]
  public function min_max_sum_average_and_count_for_non_empty() {
    Sequence::of([1, 2, 3, 4])
      ->collecting($min, Aggregations::min())
      ->collecting($max, Aggregations::max())
      ->collecting($average, Aggregations::average())
      ->collecting($sum, Aggregations::sum())
      ->collecting($count, Aggregations::count())
      ->each()
    ;
    $this->assertEquals(1, $min);
    $this->assertEquals(4, $max);
    $this->assertEquals(2.5, $average);
    $this->assertEquals(10, $sum);
    $this->assertEquals(4, $count);
  }
}