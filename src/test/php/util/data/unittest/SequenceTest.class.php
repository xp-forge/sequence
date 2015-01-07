<?php namespace util\data\unittest;

use lang\types\String;
use util\cmd\Console;
use util\data\Sequence;
use util\data\Optional;
use util\data\Collector;
use lang\IllegalStateException;

class SequenceTest extends AbstractSequenceTest {

  /**
   * Assertion helper
   *
   * @param  util.data.Sequence $seq
   * @param  function(var): void $func
   * @return void
   * @throws unittest.AssertionFailedError
   */
  protected function assertNotTwice($seq, $func) {
    $func($seq);
    try {
      $func($seq);
      $this->fail('No exception raised', null, 'lang.IllegalStateException');
    } catch (IllegalStateException $expected) {
      // OK
    }
  }

  #[@test]
  public function empty_sequence() {
    $this->assertSequence([], Sequence::$EMPTY);
  }

  #[@test]
  public function toArray_for_empty_sequence() {
    $this->assertEquals([], Sequence::$EMPTY->toArray());
  }

  #[@test, @values('util.data.unittest.Enumerables::validArrays')]
  public function toArray_returns_elements_as_array($input, $name) {
    $this->assertSequence([1, 2, 3], Sequence::of($input), $name);
  }

  #[@test]
  public function toMap_for_empty_sequence() {
    $this->assertEquals([], Sequence::$EMPTY->toMap());
  }

  #[@test, @values('util.data.unittest.Enumerables::validMaps')]
  public function toMap_returns_elements_as_map($input) {
    $this->assertEquals(['color' => 'green', 'price' => 12.99], Sequence::of($input)->toMap());
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
    Sequence::of($input)->counting($i)->each();
    $this->assertEquals(sizeof($input), $i);
  }

  #[@test, @values('util.data.unittest.Enumerables::fixedArrays')]
  public function may_use_sequence_based_on_a_fixed_enumerable_more_than_once($input) {
    $seq= Sequence::of($input);
    $seq->each();
    $seq->each();
  }

  #[@test, @values('util.data.unittest.Enumerables::streamedArrays')]
  public function cannot_use_toArray_on_a_sequence_based_on_a_streamed_enumerable_twice($input) {
    $this->assertNotTwice(Sequence::of($input), function($seq) { $seq->toArray(); });
  }

  #[@test, @values('util.data.unittest.Enumerables::streamedArrays')]
  public function cannot_use_each_on_a_sequence_based_on_a_streamed_enumerable_twice($input) {
    $this->assertNotTwice(Sequence::of($input), function($seq) { $seq->each(); });
  }

  #[@test, @values('util.data.unittest.Enumerables::streamedArrays')]
  public function cannot_use_first_on_a_sequence_based_on_a_streamed_enumerable_twice($input) {
    $this->assertNotTwice(Sequence::of($input), function($seq) { $seq->first(); });
  }

  #[@test, @values('util.data.unittest.Enumerables::streamedArrays')]
  public function cannot_use_count_on_a_sequence_based_on_a_streamed_enumerable_twice($input) {
    $this->assertNotTwice(Sequence::of($input), function($seq) { $seq->count(); });
  }

  #[@test, @values('util.data.unittest.Enumerables::streamedArrays')]
  public function cannot_use_sum_on_a_sequence_based_on_a_streamed_enumerable_twice($input) {
    $this->assertNotTwice(Sequence::of($input), function($seq) { $seq->sum(); });
  }

  #[@test, @values('util.data.unittest.Enumerables::streamedArrays')]
  public function cannot_use_min_on_a_sequence_based_on_a_streamed_enumerable_twice($input) {
    $this->assertNotTwice(Sequence::of($input), function($seq) { $seq->min(); });
  }

  #[@test, @values('util.data.unittest.Enumerables::streamedArrays')]
  public function cannot_use_max_on_a_sequence_based_on_a_streamed_enumerable_twice($input) {
    $this->assertNotTwice(Sequence::of($input), function($seq) { $seq->max(); });
  }

  #[@test, @values('util.data.unittest.Enumerables::streamedArrays')]
  public function cannot_use_collect_on_a_sequence_based_on_a_streamed_enumerable_twice($input) {
    $this->assertNotTwice(Sequence::of($input), function($seq) { $seq->collect(new Collector(
      function() { return 0; },
      function(&$r, $e) { /* Intentionally empty */ }
    )); });
  }

  #[@test, @values('util.data.unittest.Enumerables::streamedArrays')]
  public function cannot_use_reduce_on_a_sequence_based_on_a_streamed_enumerable_twice($input) {
    $this->assertNotTwice(Sequence::of($input), function($seq) { $seq->reduce(
      0,
      function($r, $e) { /* Intentionally empty */ });
    });
  }
}