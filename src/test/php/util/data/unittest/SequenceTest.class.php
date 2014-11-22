<?php namespace util\data\unittest;

use lang\types\String;
use util\cmd\Console;
use util\data\Sequence;
use util\data\Optional;
use util\data\Collector;

class SequenceTest extends AbstractSequenceTest {

  #[@test]
  public function empty_sequence() {
    $this->assertSequence([], Sequence::$EMPTY);
  }

  #[@test, @values('util.data.unittest.Enumerables::valid')]
  public function toArray_returns_elements_as_array($input, $name) {
    $this->assertSequence([1, 2, 3], Sequence::of($input), $name);
  }

  #[@test, @values([
  #  [0, []],
  #  [1, [1]],
  #  [4, [1, 2, 3, 4]]
  #])]
  public function count($length, $values) {
    $this->assertEquals($length, Sequence::of($values)->count());
  }

  #[@test, @values([
  #  [0, []],
  #  [1, [1]],
  #  [10, [1, 2, 3, 4]]
  #])]
  public function sum($result, $values) {
    $this->assertEquals($result, Sequence::of($values)->sum());
  }

  #[@test]
  public function first_returns_non_present_optional_for_empty_input() {
    $this->assertFalse(Sequence::of([])->first()->present());
  }

  #[@test]
  public function first_returns_present_optional_even_for_null() {
    $this->assertTrue(Sequence::of([null])->first()->present());
  }

  #[@test]
  public function first_returns_first_array_element() {
    $this->assertEquals(1, Sequence::of([1, 2, 3])->first()->get());
  }

  #[@test]
  public function concat() {
    $this->assertSequence([1, 2, 3, 4], Sequence::concat(Sequence::of([1, 2]), Sequence::of([3, 4])));
  }

  #[@test]
  public function concat_with_empty() {
    $this->assertSequence([3, 4], Sequence::concat(Sequence::$EMPTY, Sequence::of([3, 4])));
  }

  #[@test]
  public function concat_iteratively() {
    $seq= Sequence::$EMPTY;
    foreach ([[1, 2], [3, 4], [5, 6]] as $array) {
      $seq= Sequence::concat($seq, Sequence::of($array));
    }
    $this->assertSequence([1, 2, 3, 4, 5, 6], $seq);
  }

  #[@test, @values([
  #  [[1, 2, 3], [1, 2, 2, 3, 1, 3]],
  #  [[new String('a'), new String('b')], [new String('a'), new String('a'), new String('b')]]
  #])]
  public function distinct($result, $input) {
    $this->assertSequence($result, Sequence::of($input)->distinct());
  }

  #[@test]
  public function is_useable_inside_foreach() {
    $values= [];
    foreach (Sequence::of([1, 2, 3]) as $yielded) {
      $values[]= $yielded;
    }
    $this->assertEquals([1, 2, 3], $values);
  }

  #[@test, @values([[['a', 'b', 'c', 'd']], [[]]])]
  public function counting($input) {
    $i= 0;
    Sequence::of($input)->counting($i)->toArray();
    $this->assertEquals(sizeof($input), $i);
  }

  #[@test, @values('util.data.unittest.Enumerables::fixed')]
  public function may_use_sequence_based_on_a_fixed_enumerable_more_than_once($input) {
    $seq= Sequence::of($input);
    $seq->toArray();
    $seq->toArray();
  }

  protected function assertNotTwice($seq, $func) {
    $func($seq);
    try {
      $func($seq);
      $this->fail('No exception raised', null, 'lang.IllegalStateException');
    } catch (\lang\IllegalStateException $expected) {
      // OK
    }
  }

  #[@test, @values('util.data.unittest.Enumerables::streamed')]
  public function cannot_use_toArray_on_a_sequence_based_on_a_streamed_enumerable_twice($input) {
    $this->assertNotTwice(Sequence::of($input), function($seq) { $seq->toArray(); });
  }

  #[@test, @values('util.data.unittest.Enumerables::streamed')]
  public function cannot_use_each_on_a_sequence_based_on_a_streamed_enumerable_twice($input) {
    $this->assertNotTwice(Sequence::of($input), function($seq) { $seq->each('typeof'); });
  }

  #[@test, @values('util.data.unittest.Enumerables::streamed')]
  public function cannot_use_first_on_a_sequence_based_on_a_streamed_enumerable_twice($input) {
    $this->assertNotTwice(Sequence::of($input), function($seq) { $seq->first(); });
  }

  #[@test, @values('util.data.unittest.Enumerables::streamed')]
  public function cannot_use_count_on_a_sequence_based_on_a_streamed_enumerable_twice($input) {
    $this->assertNotTwice(Sequence::of($input), function($seq) { $seq->count(); });
  }

  #[@test, @values('util.data.unittest.Enumerables::streamed')]
  public function cannot_use_sum_on_a_sequence_based_on_a_streamed_enumerable_twice($input) {
    $this->assertNotTwice(Sequence::of($input), function($seq) { $seq->sum(); });
  }

  #[@test, @values('util.data.unittest.Enumerables::streamed')]
  public function cannot_use_min_on_a_sequence_based_on_a_streamed_enumerable_twice($input) {
    $this->assertNotTwice(Sequence::of($input), function($seq) { $seq->min(); });
  }

  #[@test, @values('util.data.unittest.Enumerables::streamed')]
  public function cannot_use_max_on_a_sequence_based_on_a_streamed_enumerable_twice($input) {
    $this->assertNotTwice(Sequence::of($input), function($seq) { $seq->max(); });
  }

  #[@test, @values('util.data.unittest.Enumerables::streamed')]
  public function cannot_use_collect_on_a_sequence_based_on_a_streamed_enumerable_twice($input) {
    $this->assertNotTwice(Sequence::of($input), function($seq) { $seq->collect(new Collector(
      function() { return 0; },
      function(&$r, $e) { /* Intentionally empty */ }
    )); });
  }

  #[@test, @values('util.data.unittest.Enumerables::streamed')]
  public function cannot_use_reduce_on_a_sequence_based_on_a_streamed_enumerable_twice($input) {
    $this->assertNotTwice(Sequence::of($input), function($seq) { $seq->reduce(
      0,
      function($r, $e) { /* Intentionally empty */ });
    });
  }
}