<?php namespace util\data\unittest;

use util\data\{CannotReset, YieldingOf};

class YieldingOfTest extends \unittest\TestCase {

  /** @return iterable */
  private function fixtures() {
    yield [function() { if (false) yield; }, []];
    yield [function() { yield; }, [null]];
    yield [function() { yield 1; }, [1]];
    yield [function() { yield 1; yield 2; }, [1, 2]];
    yield [function() { yield 'key' => 'value'; }, ['key' => 'value']];
  }

  #[@test, @values('fixtures')]
  public function iteration($generator, $expected) {
    $this->assertEquals($expected, iterator_to_array(new YieldingOf($generator())));
  }

  #[@test, @values('fixtures')]
  public function cannot_rewind_after_rewind($generator) {
    $fixture= new YieldingOf($generator());
    $fixture->rewind();

    try {
      $fixture->rewind();
      $this->fail('Expected exception not caught', null, CannotReset::class);
    } catch (CannotReset $expected) {
      // OK
    }
  }

  #[@test, @values('fixtures')]
  public function cannot_rewind_after_complete_iteration($generator) {
    $fixture= new YieldingOf($generator());
    iterator_to_array($fixture);

    try {
      $fixture->rewind();
      $this->fail('Expected exception not caught', null, CannotReset::class);
    } catch (CannotReset $expected) {
      // OK
    }
  }
}