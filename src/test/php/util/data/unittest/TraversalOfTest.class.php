<?php namespace util\data\unittest;

use util\data\TraversalOf;
use util\data\CannotReset;
use lang\IllegalStateException;

class TraversalOfTest extends \unittest\TestCase {

  #[@test, @values([
  #  [[]],
  #  [[1, 2, 3]],
  #  [['key' => 'value']]
  #])]
  public function iteration($input) {
    $this->assertEquals($input, iterator_to_array(new TraversalOf(new \ArrayIterator($input))));
  }

  #[@test, @values([
  #  [\Exception::class],
  #  [CannotReset::class],
  #  [IllegalStateException::class]
  #])]
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

    try {
      $fixture->rewind();
      $this->fail('Expected exception not caught', null, CannotReset::class);
    } catch (CannotReset $expected) {
      // OK
    }
  }

  #[@test]
  public function exceptions_during_iteration_are_left_untouched() {
    $fixture= new TraversalOf(newinstance(\Iterator::class, [], [
      'rewind'  => function() { },
      'current' => function() { throw new IllegalStateException('Test'); },
      'key'     => function() { return null; },
      'valid'   => function() { return true; },
      'next'    => function() { }
    ]));
    $fixture->rewind();

    try {
      $fixture->current();
      $this->fail('Expected exception not caught', null, IllegalStateException::class);
    } catch (IllegalStateException $expected) {
      // OK
    }
  }
}