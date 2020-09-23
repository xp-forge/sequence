<?php namespace util\data\unittest;

use lang\IllegalStateException;
use unittest\{Assert, Test, Values};
use util\data\{CannotReset, TraversalOf};

class TraversalOfTest {

  #[Test, Values([[[]], [[1, 2, 3]], [['key' => 'value']]])]
  public function iteration($input) {
    Assert::equals($input, iterator_to_array(new TraversalOf(new \ArrayIterator($input))));
  }

  #[Test, Values([[\Exception::class], [CannotReset::class], [IllegalStateException::class]])]
  public function exceptions_from_rewind_are_wrapped_in_cannot_reset($class) {
    $fixture= new TraversalOf(newinstance(\Iterator::class, [], [
      'started' => false,
      'rewind' => function() use($class) {
        if ($this->started) {
          throw new $class('Cannot reset');
        }
        $this->started= true;
      },
      'current' => function() { return null; },
      'key'     => function() { return null; },
      'valid'   => function() { return false; },
      'next'    => function() { }
    ]));
    $fixture->rewind();

    Assert::throws(CannotReset::class, function() use($fixture) {
      $fixture->rewind();
    });
  }

  #[Test]
  public function exceptions_during_iteration_are_left_untouched() {
    $fixture= new TraversalOf(newinstance(\Iterator::class, [], [
      'rewind'  => function() { },
      'current' => function() { throw new IllegalStateException('Test'); },
      'key'     => function() { return null; },
      'valid'   => function() { return true; },
      'next'    => function() { }
    ]));
    $fixture->rewind();

    Assert::throws(IllegalStateException::class, function() use($fixture) {
      $fixture->current();
    });
  }
}