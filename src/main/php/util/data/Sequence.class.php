<?php namespace util\data;

use util\Objects;
use util\Comparator;
use util\Filter;
use lang\IllegalArgumentException;
use lang\IllegalStateException;
use lang\Throwable;

/**
 * Sequences API for PHP
 *
 * @test xp://util.data.unittest.SequenceTest
 * @test xp://util.data.unittest.SequenceCreationTest
 * @test xp://util.data.unittest.SequenceSortingTest
 * @test xp://util.data.unittest.SequenceCollectionTest
 * @test xp://util.data.unittest.SequenceConcatTest
 * @test xp://util.data.unittest.SequenceFilteringTest
 * @test xp://util.data.unittest.SequenceFlatteningTest
 * @test xp://util.data.unittest.SequenceIteratorTest
 * @test xp://util.data.unittest.SequenceMappingTest
 * @test xp://util.data.unittest.SequenceReductionTest
 * @test xp://util.data.unittest.SequenceResultSetTest
 * @test xp://util.data.unittest.SequenceSkipTest
 */
class Sequence extends \lang\Object implements \IteratorAggregate {
  public static $EMPTY;

  protected $elements;

  static function __static() {
    self::$EMPTY= new self([]);
  }

  /** @param var $elements */
  protected function __construct($elements) {
    $this->elements= $elements;
  }

  /** @return util.XPIterator */
  public function iterator() { return new SequenceIterator($this); }

  /**
   * Gets an iterator on this stream. Optimizes the case that the underlying
   * elements already is an Iterator, and handles both wrappers implementing
   * the Traversable interfaces as well as primitive arrays.
   *
   * @return  php.Iterator
   */
  public function getIterator() {
    foreach ($this->elements as $key => $element) {
      yield $key => $element;
    }
  }

  /**
   * Creates a new stream with an enumeration of elements
   *
   * @see    xp://util.data.Enumeration
   * @param  var... $enumerations an iterator, iterable, generator or array
   * @return self
   * @throws lang.IllegalArgumentException if type of elements argument is incorrect
   */
  public static function of(... $enumerations) {
    if (sizeof($enumerations) > 1) {
      return new self(new Iterators($enumerations));
    } else {
      return null === $enumerations[0] ? self::$EMPTY : new self(Enumeration::of($enumerations[0]));
    }
  }

  /**
   * Creates a new stream iteratively calling the given operation, starting
   * with a given seed, and continuing with op(seed), op(op(seed)), etc.
   *
   * @param  var $seed
   * @param  function(var): var $op
   * @return self
   */
  public static function iterate($seed, $op) {
    $value= $seed;
    $closure= Functions::$UNARYOP->newInstance($op);
    return new self(new Generator(
      function() use($value) { return $value; },
      function() use(&$value, $closure) { $value= $closure($value); return $value; }
    ));
  }

  /**
   * Creates a new stream which uses a given supplier to provide the values
   *
   * @param  function(): var $supplier
   * @return self
   */
  public static function generate($supplier) {
    $closure= Functions::$SUPPLY->newInstance($supplier);
    return new self(new Generator($closure, $closure));
  }

  /**
   * Concatenates all given iteration sources
   *
   * @deprecated Use of() with multiple arguments instead!
   * @param  var... $args An iterator, iterable or an array
   * @return self
   */
  public static function concat(... $args) {
    return new self(new Iterators($args));
  }

  /**
   * Returns the first element of this stream, or an empty optional
   *
   * @param  util.Filter|function(var): bool $filter An optional filter
   * @return util.data.Optional
   * @throws util.data.CannotReset if streamed and invoked more than once
   */
  public function first($filter= null) {
    $instance= $filter ? $this->filter($filter) : $this;
    foreach ($instance->elements as $element) {
      return new Optional($element);
    }
    return Optional::$EMPTY;
  }

  /**
   * Collects all elements in an array
   *
   * @return var[]
   * @throws lang.IllegalArgumentException if streamed and invoked more than once
   */
  public function toArray() {
    $return= [];
    foreach ($this->elements as $element) {
      $return[]= $element;
    }
    return $return;
  }

  /**
   * Collects all elements in a map
   *
   * @return [:var]
   * @throws lang.IllegalArgumentException if streamed and invoked more than once
   */
  public function toMap() {
    $return= [];
    foreach ($this->elements as $key => $element) {
      $return[$key]= $element;
    }
    return $return;
  }

  /**
   * Counts all elements
   *
   * @return int
   * @throws lang.IllegalArgumentException if streamed and invoked more than once
   */
  public function count() {
    $return= 0;
    foreach ($this->elements as $element) {
      $return++;
    }
    return $return;
  }

  /**
   * Returns the smallest element.
   *
   * @param  util.Comparator|function(var, var): int $comparator default NULL
   * @return var
   * @throws lang.IllegalArgumentException if streamed and invoked more than once
   */
  public function min($comparator= null) {
    return $this->collect(Aggregations::min($comparator));
  }

  /**
   * Returns the largest element.
   *
   * @param  util.Comparator|function(var, var): int $comparator default NULL
   * @return var
   * @throws lang.IllegalArgumentException if streamed and invoked more than once
   */
  public function max($comparator= null) {
    return $this->collect(Aggregations::max($comparator));
  }

  /**
   * Performs a reduction on the elements of this stream, using the provided identity
   * value and an associative accumulation function, and returns the reduced value.
   *
   * @param  var $identity
   * @param  function(var, var): var $function
   * @return var
   * @throws lang.IllegalArgumentException if streamed and invoked more than once
   */
  public function reduce($identity, $accumulator) {
    $closure= Functions::$BINARYOP->newInstance($accumulator);
    $return= $identity;
    foreach ($this->elements as $element) {
      $return= $closure($return, $element);
    }
    return $return;
  }

  /**
   * Performs a mutable reduction operation on the elements of this stream.
   *
   * @param  util.data.ICollector $collector
   * @return var
   * @throws lang.IllegalArgumentException if streamed and invoked more than once
   */
  public function collect(ICollector $collector) {
    $accumulator= $collector->accumulator();
    $finisher= $collector->finisher();

    $return= $collector->supplier()->__invoke();
    if (Functions::$CONSUME_WITH_KEY->isInstance($accumulator)) {
      foreach ($this->elements as $key => $element) {
        $accumulator($return, $element, $key);
      }
    } else {
      foreach ($this->elements as $element) {
        $accumulator($return, $element);
      }
    }
    return $finisher ? $finisher($return) : $return;
  }

  /**
   * Invokes a given consumer on each element
   *
   * @param  function(var): void $function
   * @param  var $args Additional args to pass to function
   * @return int The number of elements
   * @throws lang.IllegalArgumentException if streamed and invoked more than once
   */
  public function each($consumer= null, $args= null) {
    if (null !== $args) {
      $inv= Functions::$APPLY->newInstance($consumer);
      $i= 0;
      foreach ($this->elements as $element) { $inv(...array_merge([$element], $args)); $i++; }
      return $i;
    } else if (Functions::$APPLY_WITH_KEY->isInstance($consumer)) {
      $inv= Functions::$APPLY_WITH_KEY->cast($consumer);
      $i= 0;
      foreach ($this->elements as $key => $element) { $inv($element, $key); $i++; }
      return $i;
    } else if (null !== $consumer) {
      $inv= Functions::$APPLY->newInstance($consumer);
      $i= 0;
      foreach ($this->elements as $element) { $inv($element); $i++; }
      return $i;
    } else {
      $i= 0;
      foreach ($this->elements as $element) { $i++; }
      return $i;
    }
  }

  /**
   * Returns a new stream with only the first `n` elements
   *
   * @param  int|function(var): bool $arg either an integer or a closure
   * @return self
   * @throws lang.IllegalArgumentException
   */
  public function limit($arg) {
    if (is_numeric($arg)) {
      $w= new \LimitIterator($this->getIterator(), 0, (int)$arg);
    } else if (Functions::$APPLY_WITH_KEY->isInstance($arg)) {
      $w= new WindowWithKey($this->getIterator(), function() { return false; }, Functions::$APPLY_WITH_KEY->cast($arg));
    } else {
      $w= new Window($this->getIterator(), function() { return false; }, Functions::$APPLY->newInstance($arg));
    }
    return new self($w);
  }

  /**
   * Returns a new stream with only the first `n` elements
   *
   * @param  int|function(var): bool $arg either an integer or a closure
   * @return self
   * @throws lang.IllegalArgumentException
   */
  public function skip($arg) {
    if (is_numeric($arg)) {
      $w= new \LimitIterator($this->getIterator(), (int)$arg, -1);
    } else if (Functions::$APPLY_WITH_KEY->isInstance($arg)) {
      $w= new WindowWithKey($this->getIterator(), Functions::$APPLY_WITH_KEY->cast($arg), function() { return false; });
    } else {
      $w= new Window($this->getIterator(), Functions::$APPLY->newInstance($arg), function() { return false; });
    }
    return new self($w);
  }

  /**
   * Returns a new stream with elements matching the given predicate
   *
   * @param  util.Filter|function(var): bool $predicate
   * @return self
   * @throws lang.IllegalArgumentException
   */
  public function filter($predicate) {
    if ($predicate instanceof Filter || is('util.Filter<?>', $predicate)) {
      $f= new Filterable($this->getIterator(), Functions::$APPLY->cast([$predicate, 'accept']));
    } else if (Functions::$APPLY_WITH_KEY->isInstance($predicate)) {
      $f= new FilterableWithKey($this->getIterator(), Functions::$APPLY_WITH_KEY->cast($predicate));
    } else {
      $f= new Filterable($this->getIterator(), Functions::$APPLY->newInstance($predicate));
    }
    return new self($f);
  }

  /**
   * Returns a new stream which maps the given function to each element
   *
   * @param  function(var): var $function
   * @return self
   * @throws lang.IllegalArgumentException
   */
  public function map($function) {
    if (Functions::$APPLY_WITH_KEY->isInstance($function)) {
      $m= new MapperWithKey($this->getIterator(), Functions::$APPLY_WITH_KEY->cast($function));
    } else {
      $m= new Mapper($this->getIterator(), Functions::$APPLY->newInstance($function));
    }
    return new self($m);
  }

  /**
   * Returns a new stream which flattens, mapping the given function to each
   * element.
   *
   * @param  function(var): var $function - if omitted, the identity function is used.
   * @return self
   * @throws lang.IllegalArgumentException
   */
  public function flatten($function= null) {
    if (null === $function) {
      $it= $this->getIterator();
    } else if (Functions::$APPLY_WITH_KEY->isInstance($function)) {
      $it= new MapperWithKey($this->getIterator(), Functions::$APPLY_WITH_KEY->cast($function), false);
    } else {
      $it= new Mapper($this->getIterator(), Functions::$APPLY->newInstance($function), false);
    }
    return new self(new Flattener($it));
  }

  /**
   * Returns a new stream which additionally calls the given function for 
   * each element it consumes. Use this e.g. for debugging purposes.
   *
   * @param  function(var): void $action
   * @param  var $args Additional args to pass to function
   * @return self
   * @throws lang.IllegalArgumentException
   */
  public function peek($action, $args= null) {
    if (null !== $args) {
      $f= Functions::$APPLY->newInstance($action);
      $p= new MapperWithKey($this->getIterator(), function($e) use($f, $args) {
        $f(...array_merge([$e], $args));
        return $e;
      });
    } else if (Functions::$APPLY_WITH_KEY->isInstance($action)) {
      $f= Functions::$APPLY_WITH_KEY->cast($action);
      $p= new MapperWithKey($this->getIterator(), function($e, $key) use($f) { $f($e, $key); return $e; });
    } else {
      $f= Functions::$APPLY->newInstance($action);
      $p= new Mapper($this->getIterator(), function($e) use($f) { $f($e); return $e; });
    }
    return new self($p);
  }

  /**
   * Returns a new stream which additionally calls the given collector for 
   * each element it consumes. Use this e.g. for statistics.
   *
   * @param  var $return A reference to the return value
   * @param  util.data.ICollector $collector
   * @return self
   * @throws lang.IllegalArgumentException
   */
  public function collecting(&$return, ICollector $collector) {
    $return= $collector->supplier()->__invoke();
    return new self(new Aggregator($this->getIterator(), $return, $collector->accumulator(), $collector->finisher()));
  }

  /**
   * Returns a new stream which counts the number of elements as iteration
   * proceeeds. A short form of `peek()` with a function incrementing a local
   * reference.
   *
   * @param  int $count Variable passed in by reference
   * @return self
   */
  public function counting(&$count) {
    return new self(new \CallbackFilterIterator($this->getIterator(), function($e) use(&$count) {
      $count++;
      return true;
    }));
  }

  /**
   * Returns a stream with distinct elements
   *
   * @param  function(var): var $function - if omitted, `util.Objects::hashOf()` is used
   * @return self
   */
  public function distinct($function= null) {
    $hash= Functions::$APPLY->newInstance($function ?: 'util.Objects::hashOf');
    return self::of(function() use($hash) {
      $set= [];
      foreach ($this->elements as $e) {
        $h= $hash($e);
        if (!isset($set[$h])) {
          $set[$h]= true;
          yield $e;
        }
      }
    });
  }

  /**
   * Returns a stream with sorted elements
   *
   * ```php
   * $seq->sorted(new ExampleComparator());   // Using comparator
   * $seq->sorted(function($a, $b) { ... });  // Using callable
   * $seq->sorted(SORT_NUMERIC | SORT_DESC);  // Using sort flags
   * $seq->sorted();                          // Default
   * ```
   *
   * @see    php://array_multisort
   * @see    php://uasort
   * @see    php://asort
   * @param  util.Comparator|function(var, var): int|int Optional sorting method
   * @return self
   */
  public function sorted($comparator= null) {
    $sort= $this->toArray();
    if ($comparator instanceof Comparator) {
      usort($sort, Functions::$COMPARATOR->newInstance([$comparator, 'compare']));
    } else if (is_int($comparator)) {
      array_multisort($sort, $comparator);
    } else if ($comparator) {
      usort($sort, Functions::$COMPARATOR->newInstance($comparator));
    } else {
      sort($sort);
    }
    return new self($sort);
  }

  /** @return string */
  public function hashCode() {
    return 'S'.Objects::hashOf($this->elements);
  }

  /**
   * Returns whether this sequence equals a given value.
   *
   * @param  var $cmp
   * @return bool
   */
  public function equals($cmp) {
    return $cmp instanceof self && Objects::equal($this->elements, $cmp->elements);
  }

  /**
   * Creates a string representation of this sequence
   *
   * @return string
   */
  public function toString() {
    if ([] === $this->elements) {
      return nameof($this).'<EMPTY>';
    } else {
      return nameof($this).'@'.Objects::stringOf($this->elements);
    }
  }
}