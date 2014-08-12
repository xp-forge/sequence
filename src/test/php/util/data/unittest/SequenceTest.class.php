<?php namespace util\data\unittest;

use lang\types\String;
use util\cmd\Console;
use util\data\Sequence;
use util\data\Optional;
use util\data\Collector;
use util\Date;
use io\streams\MemoryOutputStream;

class SequenceTest extends AbstractSequenceTest {

  #[@test]
  public function empty_sequence() {
    $this->assertSequence([], Sequence::$EMPTY);
  }

  #[@test, @values('util.data.unittest.Enumerables::valid')]
  public function toArray_returns_elements_as_array($input, $name) {
    $this->assertSequence([1, 2, 3], Sequence::of($input), $name);
  }

  #[@test]
  public function filter() {
    $this->assertSequence([2, 4], Sequence::of([1, 2, 3, 4])->filter(function($e) { return 0 === $e % 2; }));
  }

  #[@test]
  public function filter_with_is_string_native_function() {
    $this->assertSequence(['Hello', 'World'], Sequence::of(['Hello', 1337, 'World'])->filter('is_string'));
  }

  #[@test]
  public function filter_with_generic_filter_instance() {
    $this->assertSequence(['Hello', 'World'], Sequence::of(['Hello', '', 'World'])->filter(newinstance('util.Filter<string>', [], [
      'accept' => function($e) { return strlen($e) > 0; }
    ])));
  }

  #[@test]
  public function filter_with_filter_instance() {
    $this->assertSequence(['Hello', 'World'], Sequence::of(['Hello', '', 'World'])->filter(newinstance('util.Filter', [], [
      'accept' => function($e) { return strlen($e) > 0; }
    ])));
  }

  #[@test, @values('noncallables'), @expect('lang.IllegalArgumentException')]
  public function filter_raises_exception_when_given($noncallable) {
    Sequence::of([])->filter($noncallable);
  }

  #[@test]
  public function map() {
    $this->assertSequence([2, 4, 6, 8], Sequence::of([1, 2, 3, 4])->map(function($e) { return $e * 2; }));
  }

  #[@test]
  public function map_with_with_floor_native_function() {
    $this->assertSequence([1.0, 2.0, 3.0], Sequence::of([1.9, 2.5, 3.1])->map('floor'));
  }

  #[@test, @values('noncallables'), @expect('lang.IllegalArgumentException')]
  public function map_raises_exception_when_given($noncallable) {
    Sequence::of([])->map($noncallable);
  }

  #[@test]
  public function flatten_without_mapper() {
    $this->assertSequence(['a', 'b', 'c', 'd'], Sequence::of([['a', 'b'], ['c', 'd']])->flatten());
  }

  #[@test]
  public function flatten_with_mapper() {
    $this->assertSequence(['a', 'b', 'c', 'd'], Sequence::of(['a', 'c'])->flatten(function($e) {
      return Sequence::iterate($e, function($n) { return ++$n; })->limit(2);
    }));
  }

  #[@test]
  public function flatten_optionals() {
    $this->assertSequence(['a', 'b'], Sequence::of([Optional::of('a'), Optional::$EMPTY, Optional::of('b')])->flatten());
  }

  #[@test, @values('noncallables'), @expect('lang.IllegalArgumentException')]
  public function flatten_raises_exception_when_given($noncallable) {
    if (null === $noncallable) {
      throw new \lang\IllegalArgumentException('Valid use-case');
    }
    Sequence::of([])->flatten($noncallable);
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

  #[@test]
  public function min_using_comparator() {
    $this->assertEquals(
      new Date('1977-12-14'),
      Sequence::of([new Date('1977-12-14'), new Date('2014-07-17'), new Date('1979-12-29')])->min(newinstance('util.Comparator', [], [
        'compare' => function($a, $b) { return $b->compareTo($a); }
      ]))
    );
  }

  #[@test]
  public function min_using_closure() {
    $this->assertEquals(
      new Date('1977-12-14'),
      Sequence::of([new Date('1977-12-14'), new Date('2014-07-17'), new Date('1979-12-29')])->min(function($a, $b) {
        return $b->compareTo($a);
      })
    );
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
  public function max_using_comparator() {
    $this->assertEquals(
      new Date('2014-07-17'),
      Sequence::of([new Date('1977-12-14'), new Date('2014-07-17'), new Date('1979-12-29')])->max(newinstance('util.Comparator', [], [
        'compare' => function($a, $b) { return $b->compareTo($a); }
      ]))
    );
  }

  #[@test]
  public function max_using_closure() {
    $this->assertEquals(
      new Date('2014-07-17'),
      Sequence::of([new Date('1977-12-14'), new Date('2014-07-17'), new Date('1979-12-29')])->max(function($a, $b) {
        return $b->compareTo($a);
      })
    );
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
  public function reduce_used_for_max_with_native_max_function() {
    $this->assertEquals(10, Sequence::of([7, 1, 10, 3])->reduce(0, 'max'));
  }

  #[@test]
  public function reduce_used_for_concatenation() {
    $this->assertEquals('Hello World', Sequence::of(['Hello', ' ', 'World'])->reduce('', function($a, $b) {
      return $a.$b;
    }));
  }

  #[@test]
  public function collect_used_for_averaging() {
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
  public function each() {
    $collect= [];
    Sequence::of([1, 2, 3, 4])->each(function($e) use(&$collect) {
      $collect[]= $e;
    });
    $this->assertEquals([1, 2, 3, 4], $collect);
  }

  #[@test, @values([[[1, 2, 3, 4]], [[]]])]
  public function each_returns_number_of_processed_elements($input) {
    $this->assertEquals(sizeof($input), Sequence::of($input)->each(function($e) { }));
  }

  #[@test]
  public function each_writing_to_stream() {
    $out= new MemoryOutputStream();

    Sequence::of([1, 2, 3, 4])->each([$out, 'write']);

    $this->assertEquals('1234', $out->getBytes());
  }

  #[@test]
  public function each_writing_to_console_out() {
    $orig= Console::$out->getStream();
    $out= new MemoryOutputStream();
    Console::$out->setStream($out);

    Sequence::of([1, 2, 3, 4])->each('util.cmd.Console::write');

    Console::$out->setStream($orig);    
    $this->assertEquals('1234', $out->getBytes());
  }

  #[@test]
  public function each_with_var_export() {
    ob_start();

    Sequence::of([1, 2, 3, 4])->each('var_export');

    $bytes= ob_get_contents();
    ob_end_clean();   
    $this->assertEquals('1234', $bytes);
  }

  #[@test, @values('noncallables'), @expect('lang.IllegalArgumentException')]
  public function each_raises_exception_when_given($noncallable) {
    Sequence::of([])->each($noncallable);
  }

  #[@test]
  public function limit_stops_at_nth_array_element() {
    $this->assertSequence([1, 2], Sequence::of([1, 2, 3])->limit(2));
  }

  #[@test]
  public function limit_stops_at_nth_iterator_element() {
    $this->assertSequence([1, 2], Sequence::iterate(1, function($i) { return ++$i; })->limit(2));
  }

  #[@test]
  public function limit_stops_at_nth_generator_element() {
    $i= 1;
    $this->assertSequence([1, 2], Sequence::generate(function() use(&$i) { return $i++; })->limit(2));
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

  #[@test]
  public function skip_excludes_n_first_elements() {
    $this->assertSequence([3, 4], Sequence::of([1, 2, 3, 4])->skip(2));
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

  #[@test]
  public function peeking() {
    $debug= [];
    Sequence::of([1, 2, 3, 4])
      ->filter(function($e) { return $e % 2 > 0; })
      ->peek(function($e) use(&$debug) { $debug[]= $e; })
      ->toArray()   // or any other terminal action
    ;
    $this->assertEquals([1, 3], $debug);
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