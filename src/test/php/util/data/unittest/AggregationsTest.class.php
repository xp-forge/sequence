<?php namespace util\data\unittest;

use unittest\Assert;
use util\data\Aggregations;
use util\data\Sequence;

class AggregationsTest {

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
    Assert::null($min);
    Assert::null($max);
    Assert::null($average);
    Assert::null($sum);
    Assert::equals(0, $count);
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
    Assert::equals(1, $min);
    Assert::equals(4, $max);
    Assert::equals(2.5, $average);
    Assert::equals(10, $sum);
    Assert::equals(4, $count);
  }
}