<?php namespace util\data\unittest;

use lang\types\String;
use util\cmd\Console;
use util\data\Sequence;
use util\data\Collector;
use io\streams\MemoryOutputStream;

class SequenceTest extends AbstractSequenceTest {

  /**
   * Assertion helper
   *
   * @param  var[] $expected
   * @param  util.data.Sequence $sequence
   * @param  string $message
   * @throws unittest.AssertionFailedError
   */
  protected function assertSequence($expected, $sequence, $message= '!=') {
    $this->assertEquals($expected, $sequence->toArray(), $message);
  }

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
  public function skip_excludes_n_first_elements() {
    $this->assertSequence([3, 4], Sequence::of([1, 2, 3, 4])->skip(2));
  }

  #[@test, @values([
  #  [[1, 2, 3], [1, 2, 2, 3, 1, 3]],
  #  [[new String("a"), new String("b")], [new String("a"), new String("a"), new String("b")]]
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
}