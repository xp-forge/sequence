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
      '#[\ReturnTypeWillChange] rewind' => function() use($class) {
        if ($this->started) {
          throw new $class('Cannot reset');
        }
        $this->started= true;
      },
      '#[ReturnTypeWillChange] current' => function() { return null; },
      '#[ReturnTypeWillChange] key'     => function() { return null; },
      '#[ReturnTypeWillChange] valid'   => function() { return false; },
      '#[ReturnTypeWillChange] next'    => function() { }
    ]));
    $fixture->rewind();

    Assert::throws(CannotReset::class, function() use($fixture) {
      $fixture->rewind();
    });
  }

  #[Test]
  public function exceptions_during_iteration_are_left_untouched() {
    $fixture= new TraversalOf(newinstance(\Iterator::class, [], [
      '#[ReturnTypeWillChange] rewind'  => function() { },
      '#[ReturnTypeWillChange] current' => function() { throw new IllegalStateException('Test'); },
      '#[ReturnTypeWillChange] key'     => function() { return null; },
      '#[ReturnTypeWillChange] valid'   => function() { return true; },
      '#[ReturnTypeWillChange] next'    => function() { }
    ]));
    $fixture->rewind();

    Assert::throws(IllegalStateException::class, function() use($fixture) {
      $fixture->current();
    });
  }
}