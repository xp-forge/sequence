<?php namespace util\data\unittest;

use lang\types\String;
use util\data\Sequence;
use util\data\Collector;

class SequenceTest extends \unittest\TestCase {

  #[@test]
  public function can_create_via_of() {
    $this->assertInstanceOf('util.data.Sequence', Sequence::of([1, 2, 3]));
  }

  #[@test]
  public function can_create_via_iterate() {
    $this->assertInstanceOf('util.data.Sequence', Sequence::iterate(0, function($i) { return $i++; }));
  }

  #[@test]
  public function can_create_via_generate() {
    $this->assertInstanceOf('util.data.Sequence', Sequence::generate(function() { return rand(1, 1000); }));
  }

  #[@test, @values([
  #  [[1, 2, 3]],
  #  [new \lang\types\ArrayList(1, 2, 3)]
  #])]
  public function toArray_returns_elements_as_array($input) {
    $this->assertEquals([1, 2, 3], Sequence::of($input)->toArray());
  }

  #[@test]
  public function filter() {
    $this->assertEquals([2, 4], Sequence::of([1, 2, 3, 4])
      ->filter(function($e) { return 0 === $e % 2; })
      ->toArray()
    );
  }

  #[@test]
  public function map() {
    $this->assertEquals([2, 4, 6, 8], Sequence::of([1, 2, 3, 4])
      ->map(function($e) { return $e * 2; })
      ->toArray()
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

  #[@test, @values([
  #  [0, []],
  #  [1, [1]],
  #  [10, [1, 2, 3, 4]]
  #])]
  public function sum($result, $values) {
    $this->assertEquals($result, Sequence::of($values)->sum());
  }

  #[@test, @values([
  #  [null, []],
  #  [1, [1]],
  #  [2, [10, 7, 2]]
  #])]
  public function min($result, $values) {
    $this->assertEquals($result, Sequence::of($values)->min());
  }

  #[@test, @values([
  #  [null, []],
  #  [1, [1]],
  #  [10, [2, 10, 7]]
  #])]
  public function max($result, $values) {
    $this->assertEquals($result, Sequence::of($values)->max());
  }

  #[@test]
  public function reduce_returns_identity_for_empty_input() {
    $this->assertEquals(-1, Sequence::of([])->reduce(-1, function($a, $b) {
      $this->fail('Should not be called');
    }));
  }

  #[@test]
  public function reduce_used_for_summing() {
    $this->assertEquals(10, Sequence::of([1, 2, 3, 4])->reduce(0, function($a, $b) {
      return $a + $b;
    }));
  }

  #[@test]
  public function reduce_used_for_max() {
    $this->assertEquals(10, Sequence::of([7, 1, 10, 3])->reduce(0, function($a, $b) {
      return max($a, $b);
    }));
  }

  #[@test]
  public function reduce_used_for_concatenation() {
    $this->assertEquals('Hello World', Sequence::of(['Hello', ' ', 'World'])->reduce('', function($a, $b) {
      return $a.$b;
    }));
  }

  #[@test]
  public function collect() {
    $result= Sequence::of([1, 2, 3, 4])->collect(new Collector(
      function() { return ['total' => 0, 'sum' => 0]; },
      function(&$result, $arg) { $result['total']++; $result['sum']+= $arg; }
    ));
    $this->assertEquals(2.5, $result['sum'] / $result['total']);
  }

  #[@test]
  public function collect_used_for_joining() {
    $result= Sequence::of(['a', 'b', 'c'])->collect(new Collector(
      function() { return ''; },
      function(&$result, $arg) { $result.= ', '.$arg; },
      function($result) { return substr($result, 2); }
    ));
    $this->assertEquals('a, b, c', $result);
  }

  #[@test]
  public function first_returns_null_for_empty_input() {
    $this->assertNull(Sequence::of([])->first());
  }

  #[@test]
  public function first_returns_first_array_element() {
    $this->assertEquals(1, Sequence::of([1, 2, 3])->first());
  }

  #[@test]
  public function each() {
    $collect= [];
    Sequence::of([1, 2, 3, 4])->each(function($e) use(&$collect) { $collect[]= $e; });
    $this->assertEquals([1, 2, 3, 4], $collect);
  }

  #[@test]
  public function limit_stops_at_nth_array_element() {
    $this->assertEquals([1, 2], Sequence::of([1, 2, 3])->limit(2)->toArray());
  }

  #[@test]
  public function limit_stops_at_nth_iterator_element() {
    $this->assertEquals([1, 2], Sequence::iterate(1, function($i) { return ++$i; })
      ->limit(2)
      ->toArray()
    );
  }

  #[@test]
  public function limit_stops_at_nth_generator_element() {
    $i= 1;
    $this->assertEquals([1, 2], Sequence::generate(function() use(&$i) { return $i++; })
      ->limit(2)
      ->toArray()
    );
  }

  #[@test]
  public function concat() {
    $this->assertEquals([1, 2, 3, 4], Sequence::concat(Sequence::of([1, 2]), Sequence::of([3, 4]))
      ->toArray()
    );
  }

  #[@test, @values([
  #  [[1, 2, 3], [1, 2, 2, 3, 1, 3]],
  #  [[new String("a"), new String("b")], [new String("a"), new String("a"), new String("b")]]
  #])]
  public function distinct($result, $input) {
    $this->assertEquals($result, Sequence::of($input)->distinct()->toArray());
  }

  #[@test]
  public function is_useable_inside_foreach() {
    $values= [];
    foreach (Sequence::of([1, 2, 3]) as $yielded) {
      $values[]= $yielded;
    }
    $this->assertEquals([1, 2, 3], $values);
  }
}