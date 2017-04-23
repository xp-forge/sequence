<?php namespace util\data\unittest;

use util\cmd\Console;
use util\data\Sequence;
use util\data\Optional;
use util\data\Collector;
use util\data\CannotReset;

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
      $this->fail('No exception raised', null, CannotReset::class);
    } catch (CannotReset $expected) {
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
  public function toArray_optionally_accepts_mapper() {
    $this->assertEquals(
      [2, 4],
      Sequence::of([1, 2])->toArray(function($v) { return $v * 2; })
    );
  }

  #[@test]
  public function toMap_for_empty_sequence() {
    $this->assertEquals([], Sequence::$EMPTY->toMap());
  }

  #[@test, @values('util.data.unittest.Enumerables::validMaps')]
  public function toMap_returns_elements_as_map($input) {
    $this->assertEquals(['color' => 'green', 'price' => 12.99], Sequence::of($input)->toMap());
  }

  #[@test]
  public function toMap_optionally_accepts_mapper() {
    $this->assertEquals(
      ['a' => 2, 'b' => 4],
      Sequence::of(['a' => 1, 'b' => 2])->toMap(function($v) { return $v * 2; })
    );
  }

  #[@test, @values([
  #  [0, []],
  #  [1, [1]],
  #  [4, [1, 2, 3, 4]]
  #])]
  public function count($length, $values) {
    $this->assertEquals($length, Sequence::of($values)->count());
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
  public function first_returns_first_element_to_match_its_filter() {
    $this->assertEquals(2, Sequence::of([1, 2, 3])->first(function($i) { return 0 === $i % 2; })->get());
  }

  #[@test]
  public function first_returns_non_present_optional_if_no_element_matches_its_filter() {
    $this->assertFalse(Sequence::of([1, 3])->first(function($i) { return 0 === $i % 2; })->present());
  }

  #[@test, @values([
  #  [[1, 2, 3], [1, 2, 2, 3, 1, 3]],
  #  [[new Name('a'), new Name('b')], [new Name('a'), new Name('a'), new Name('b')]]
  #])]
  public function distinct($result, $input) {
    $this->assertSequence($result, Sequence::of($input)->distinct());
  }

  #[@test, @values([
  #  function($record) { return $record['id']; }
  #])]
  public function distinct_with($function) {
    $this->assertSequence(
      [['id' => 1, 'name' => 'Timm']],
      Sequence::of([['id' => 1, 'name' => 'Timm'], ['id' => 1]])->distinct($function)
    );
  }

  #[@test]
  public function is_useable_inside_foreach() {
    $this->assertEquals([1, 2, 3], iterator_to_array(Sequence::of([1, 2, 3])));
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

  #[@test]
  public function toString_for_empty_sequence() {
    $this->assertEquals('util.data.Sequence<EMPTY>', Sequence::$EMPTY->toString());
  }

  #[@test]
  public function toString_for_sequence_of_array() {
    $this->assertEquals('util.data.Sequence@[1, 2, 3]', Sequence::of([1, 2, 3])->toString());
  }
}