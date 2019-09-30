<?php namespace util\data\unittest;

use lang\IllegalArgumentException;
use unittest\actions\VerifyThat;
use util\data\Sequence;

class SequenceMappingTest extends AbstractSequenceTest {

  #[@test]
  public function with_function() {
    $this->assertSequence([2, 4, 6, 8], Sequence::of([1, 2, 3, 4])->map(function($e) { return $e * 2; }));
  }

  #[@test]
  public function with_with_floor_native_function() {
    $this->assertSequence([1.0, 2.0, 3.0], Sequence::of([1.9, 2.5, 3.1])->map('floor'));
  }

  #[@test, @values('noncallables'), @expect(IllegalArgumentException::class)]
  public function map_raises_exception_when_given($noncallable) {
    Sequence::of([])->map($noncallable);
  }

  #[@test]
  public function array_index_is_passed_to_function() {
    $keys= [];
    Sequence::of([1, 2, 3])->map(function($e, $key) use(&$keys) { $keys[]= $key; return $e; })->each();
    $this->assertEquals([0, 1, 2], $keys);
  }

  #[@test]
  public function map_key_is_passed_to_function() {
    $keys= [];
    Sequence::of(['one' => 1, 'two' => 2, 'three' => 3])->map(function($e, $key) use(&$keys) { $keys[]= $key; return $e; })->each();
    $this->assertEquals(['one', 'two', 'three'], $keys);
  }

  #[@test]
  public function with_instance_method() {
    $people= [new Person(1549, 'Timm'), new Person(6100, 'Test')];
    $this->assertSequence(
      ['Timm', 'Test'],
      Sequence::of($people)->map('util.data.unittest.Person::name')
    );
  }

  #[@test]
  public function with_generator() {
    $records= Sequence::of([['unit' => 'yellow', 'amount' => 20], ['unit' => 'blue', 'amount' => 19]]);
    $generator= function($record) { yield $record['unit'] => $record['amount']; };
    $this->assertEquals(['yellow' => 20, 'blue' => 19], $records->map($generator)->toMap());
  }

  #[@test]
  public function with_generator_and_key() {
    $records= Sequence::of(['color' => 'green', 'price' => 12.99]);
    $generator= function($value, $key) { yield strtoupper($key) => $value; };
    $this->assertEquals(['COLOR' => 'green', 'PRICE' => 12.99], $records->map($generator)->toMap());
  }
}