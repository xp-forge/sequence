<?php namespace util\data\unittest;

use lang\IllegalArgumentException;
use test\verify\Condition;
use test\{Assert, Expect, Test, Values};
use util\data\Sequence;

class SequenceMappingTest extends AbstractSequenceTest {

  #[Test]
  public function with_function() {
    $this->assertSequence([2, 4, 6, 8], Sequence::of([1, 2, 3, 4])->map(function($e) { return $e * 2; }));
  }

  #[Test]
  public function with_with_floor_native_function() {
    $this->assertSequence([1.0, 2.0, 3.0], Sequence::of([1.9, 2.5, 3.1])->map('floor'));
  }

  #[Test]
  public function with_null_is_noop() {
    $sequence= Sequence::of([1, 2, 3, 4]);
    Assert::true($sequence === $sequence->map(null));
  }

  #[Test, Values(from: 'noncallables'), Expect(IllegalArgumentException::class)]
  public function map_raises_exception_when_given($noncallable) {
    Sequence::of([])->map($noncallable);
  }

  #[Test]
  public function array_index_is_passed_to_function() {
    $keys= [];
    Sequence::of([1, 2, 3])->map(function($e, $key) use(&$keys) { $keys[]= $key; return $e; })->each();
    Assert::equals([0, 1, 2], $keys);
  }

  #[Test]
  public function map_key_is_passed_to_function() {
    $keys= [];
    Sequence::of(['one' => 1, 'two' => 2, 'three' => 3])->map(function($e, $key) use(&$keys) { $keys[]= $key; return $e; })->each();
    Assert::equals(['one', 'two', 'three'], $keys);
  }

  #[Test]
  public function with_instance_method() {
    $people= [new Person(1549, 'Timm'), new Person(6100, 'Test')];
    $this->assertSequence(
      ['Timm', 'Test'],
      Sequence::of($people)->map('util.data.unittest.Person::name')
    );
  }

  #[Test]
  public function with_generator() {
    $records= Sequence::of([['unit' => 'yellow', 'amount' => 20], ['unit' => 'blue', 'amount' => 19]]);
    $generator= function($record) { yield $record['unit'] => $record['amount']; };
    Assert::equals(['yellow' => 20, 'blue' => 19], $records->map($generator)->toMap());
  }

  #[Test]
  public function with_generator_and_key() {
    $records= Sequence::of(['color' => 'green', 'price' => 12.99]);
    $generator= function($value, $key) { yield strtoupper($key) => $value; };
    Assert::equals(['COLOR' => 'green', 'PRICE' => 12.99], $records->map($generator)->toMap());
  }
}