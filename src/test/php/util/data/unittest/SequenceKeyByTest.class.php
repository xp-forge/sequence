<?php namespace util\data\unittest;

use test\{Assert, Test, Values};
use util\data\Sequence;

class SequenceKeyByTest {

  #[Test]
  public function key_by_key() {
    $one= ['_id' => 'one', 'user' => ['power' => 1000]];
    $two= ['_id' => 'two', 'user' => ['power' => 2000]];

    Assert::equals(['one' => $one, 'two' => $two], Sequence::of([$one, $two])->keyBy('_id'));
  }

  #[Test, Values([
    [['_id' => 'power'], ['one' => 1000, 'two' => 2000]],
    [['power' => '_id'], [1000 => 'one',  2000 => 'two']]
  ])]
  public function key_by_map($fields, $expected) {
    $one= ['_id' => 'one', 'power' => 1000];
    $two= ['_id' => 'two', 'power' => 2000];

    Assert::equals($expected, Sequence::of([$one, $two])->keyBy($fields));
  }

  #[Test]
  public function key_by_callable() {
    $one= ['_id' => 'one', 'user' => ['power' => 1000]];
    $two= ['_id' => 'two', 'user' => ['power' => 2000]];

    Assert::equals(
      ['one' => 1000, 'two' => 2000],
      Sequence::of([$one, $two])->keyBy(fn($r) => [$r['_id'] => $r['user']['power']])
    );
  }

  #[Test]
  public function key_by_generator() {
    $one= ['_id' => 'one', 'user' => ['power' => 1000]];
    $two= ['_id' => 'two', 'user' => ['power' => 2000]];

    Assert::equals(
      ['one' => 1000, 'two' => 2000],
      Sequence::of([$one, $two])->keyBy(fn($r) => yield $r['_id'] => $r['user']['power'])
    );
  }
}