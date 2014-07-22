<?php namespace util\data;

use util\Objects;
use util\Comparator;
use lang\IllegalArgumentException;

/**
 * Sequences API for PHP
 *
 * @test xp://util.data.unittest.SequenceTest
 * @test xp://util.data.unittest.SequenceCreationTest
 * @test xp://util.data.unittest.SequenceSortingTest
 */
#[@generic(self= 'T')]
class Sequence extends \lang\Object implements \IteratorAggregate {
  public static $EMPTY;

  protected $elements;

  static function __static() {
    self::$EMPTY= new self([]);
  }

  protected function __construct($elements) {
    $this->elements= $elements;
  }

  /**
   * Gets an iterator on this stream. Optimizes the case that the underlying
   * elements already is an Iterator, and handles both wrappers implementing
   * the Traversable interfaces as well as primitive arrays.
   *
   * @return  php.Iterator
   */
  public function getIterator() {
    if ($this->elements instanceof \Iterator) {
      return $this->elements;
    } else if ($this->elements instanceof \Traversable) {
      return new \IteratorIterator($this->elements);
    } else {
      return new \ArrayIterator($this->elements);
    }
  }

  /**
   * Creates a new stream with an enumeration of elements
   *
   * @see    xp://util.data.Enumeration
   * @param  var $elements an iterator, iterable, generator or array
   * @return self<R>
   * @throws lang.IllegalArgumentException if type of elements argument is incorrect
   */
  #[@generic(return= 'self<R>')]
  public static function of($elements) {
    return new self(Enumeration::of($elements));
  }

  /**
   * Creates a new stream iteratively calling the given operation, starting
   * with a given seed, and continuing with op(seed), op(op(seed)), etc.
   *
   * @param  R $seed
   * @param  function<R: R> $op
   * @return self<R>
   */
  #[@generic(return= 'self<R>')]
  public static function iterate($seed, $op) {
    $value= $seed;
    $closure= Closure::of($op);
    return new self(new Generator(
      function() use($value) { return $value; },
      function() use(&$value, $closure) { $value= $closure($value); return $value; }
    ));
  }

  /**
   * Creates a new stream which uses a given supplier to provide the values
   *
   * @param  function<(): R> $supplier
   * @return self<R>
   */
  #[@generic(return= 'self<R>')]
  public static function generate($supplier) {
    $closure= Closure::of($supplier);
    return new self(new Generator($closure, $closure));
  }

  /**
   * Concatenates two streams
   *
   * @param  self<R> $a
   * @param  self<R> $b
   * @return self<R>
   */
  #[@generic(params= 'self<R>, self<R>', return= 'self<R>')]
  public static function concat(self $a, self $b) {
    $it= new \AppendIterator();
    $it->append($a->getIterator());
    $it->append($b->getIterator());
    return new self($it);
  }

  /**
   * Returns the first element of this stream, or NULL
   *
   * @return util.data.Optional<T>
   */
  #[@generic(return= 'T')]
  public function first() {
    foreach ($this->elements as $element) {
      return Optional::of($element);
    }
    return Optional::$EMPTY;
  }

  /**
   * Collects all elements in an array
   *
   * @return T[]
   */
  #[@generic(return= 'T[]')]
  public function toArray() {
    $return= [];
    foreach ($this->elements as $element) {
      $return[]= $element;
    }
    return $return;
  }

  /**
   * Counts all elements
   *
   * @return int
   */
  public function count() {
    $return= 0;
    foreach ($this->elements as $element) {
      $return++;
    }
    return $return;
  }

  /**
   * Returns the sum of all elements
   *
   * @return T
   */
  #[@generic(return= 'T')]
  public function sum() {
    $return= 0;
    foreach ($this->elements as $element) {
      $return+= $element;
    }
    return $return;
  }

  /**
   * Helper for min() and max()
   *
   * @param  var $comparator Either a Comparator or a closure to compare.
   * @param  int $n direction, either -1 or +1
   * @return T
   */
  protected function select($comparator, $n) {
    $return= null;
    if ($comparator instanceof Comparator) {
      $cmp= Closure::of([$comparator, 'compare']);
    } else {
      $cmp= Closure::of($comparator);
    }
    foreach ($this->elements as $element) {
      if (null === $return || $cmp($element, $return) * $n > 0) $return= $element;
    }
    return $return;
  }

  /**
   * Returns the smallest element. Optimized for the case when the no comparator
   * is given, using the `<` operator.
   *
   * @param  var $comparator default NULL Either a Comparator or a closure to compare.
   * @return T
   */
  #[@generic(return= 'T')]
  public function min($comparator= null) {
    if (null === $comparator) {
      $return= null;
      foreach ($this->elements as $element) {
        if (null === $return || $element < $return) $return= $element;
      }
      return $return;
    }
    return $this->select($comparator, -1);
  }

  /**
   * Returns the largest element. Optimized for the case when no comparator is 
   * given, using the `>` operator.
   *
   * @param  var $comparator default NULL Either a Comparator or a closure to compare.
   * @return T
   */
  #[@generic(return= 'T')]
  public function max($comparator= null) {
    if (null === $comparator) {
      $return= null;
      foreach ($this->elements as $element) {
        if (null === $return || $element > $return) $return= $element;
      }
      return $return;
    }
    return $this->select($comparator, +1);
  }

  /**
   * Performs a reduction on the elements of this stream, using the provided identity
   * value and an associative accumulation function, and returns the reduced value.
   *
   * @param  T $identity
   * @param  function<T, T: T> $function
   * @return self<T>
   */
  public function reduce($identity, $accumulator) {
    $closure= Closure::of($accumulator);
    $return= $identity;
    foreach ($this->elements as $element) {
      $return= $closure($return, $element);
    }
    return $return;
  }

  /**
   * Performs a mutable reduction operation on the elements of this stream.
   *
   * @param  util.data.ICollector
   * @return R
   */
  public function collect(ICollector $collector) {
    $accumulator= $collector->accumulator();
    $finisher= $collector->finisher();

    $return= $collector->supplier()->__invoke();
    foreach ($this->elements as $element) {
      $accumulator($return, $element);
    }

    return $finisher ? $finisher($return) : $return;
  }

  /**
   * Invokes a given consumer on each element
   *
   * @param  function<T> $function
   * @return int The number of elements
   */
  public function each($consumer) {
    $inv= Closure::of($consumer);
    $i= 0;
    foreach ($this->elements as $element) {
      $inv($element);
      $i++;
    }
    return $i;
  }

  /**
   * Returns a new stream with only the first `n` elements
   *
   * @param  var $arg either an integer or a closure
   * @return self<T>
   */
  #[@generic(return= 'self<T>')]
  public function limit($arg) {
    if (is_numeric($arg)) {
      return new self(new \LimitIterator($this->getIterator(), 0, (int)$arg));
    } else {
      return new self(new Window($this->getIterator(), function() { return false; }, Closure::of($arg)));
    }
  }

  /**
   * Returns a new stream with only the first `n` elements
   *
   * @param  var $arg either an integer or a closure
   * @return self<T>
   */
  #[@generic(return= 'self<T>')]
  public function skip($arg) {
    if (is_numeric($arg)) {
      return new self(new \LimitIterator($this->getIterator(), (int)$arg, -1));
    } else {
      return new self(new Window($this->getIterator(), Closure::of($arg), function() { return false; }));
    }
  }

  /**
   * Returns a new stream with elements matching the given predicta
   *
   * @param  function<T: bool> $function
   * @return self<T>
   */
  #[@generic(return= 'self<T>')]
  public function filter($predicate) {
    return new self(new Filterable($this->getIterator(), Closure::of($predicate)));
  }

  /**
   * Returns a new stream which maps the given function to each element
   *
   * @param  function<T: R> $function
   * @return self<R>
   */
  #[@generic(return= 'self<R>')]
  public function map($function) {
    return new self(new Mapper($this->getIterator(), Closure::of($function)));
  }

  /**
   * Returns a new stream which additionally calls the given function for 
   * each element it consumes. Use this e.g. for debugging purposes.
   *
   * @param  function<T: R> $action
   * @return self<R>
   */
  #[@generic(return= 'self<R>')]
  public function peek($action) {
    $f= Closure::of($action);
    return new self(new \CallbackFilterIterator($this->getIterator(), function($e) use($f) {
      $f($e);
      return true;
    }));
  }

  /**
   * Returns a new stream which counts the number of elements as iteration
   * proceeeds. A short form of `peek()` with a function incrementing a local
   * reference.
   *
   * @param  int $count Variable passed in by reference
   * @return self<R>
   */
  #[@generic(return= 'self<R>')]
  public function counting(&$count) {
    return new self(new \CallbackFilterIterator($this->getIterator(), function($e) use(&$count) {
      $count++;
      return true;
    }));
  }


  /**
   * Returns a stream with distinct elements
   *
   * @return self<T>
   */
  #[@generic(return= 'self<T>')]
  public function distinct() {
    $set= [];
    return new self(new \CallbackFilterIterator($this->getIterator(), function($e) use(&$set) {
      $h= Objects::hashOf($e);
      if (isset($set[$h])) {
        return false;
      } else {
        $set[$h]= true;
        return true;
      }
    }));
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
   * @param  var $comparator either a Comparator instance, a callable or optional sort flags
   * @return self<T>
   */
  #[@generic(return= 'self<T>')]
  public function sorted($comparator= null) {
    $sort= $this->toArray();
    if ($comparator instanceof Comparator) {
      uasort($sort, Closure::of([$comparator, 'compare']));
    } else if (is_int($comparator)) {
      array_multisort($sort, $comparator);
    } else if ($comparator) {
      uasort($sort, Closure::of($comparator));
    } else {
      asort($sort);
    }
    return new self($sort);
  }
}