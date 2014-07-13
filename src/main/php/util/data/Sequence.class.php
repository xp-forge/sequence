<?php namespace util\data;

use util\Objects;
use lang\IllegalArgumentException;

/**
 * Sequences API for PHP
 *
 * @test xp://util.data.unittest.SequenceTest
 * @test xp://util.data.unittest.SequenceCreationTest
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
   * Returns the smallest element
   *
   * @return T
   */
  #[@generic(return= 'T')]
  public function min() {
    $return= null;
    foreach ($this->elements as $element) {
      if (null === $return || $element < $return) $return= $element;
    }
    return $return;
  }

  /**
   * Returns the largest element
   *
   * @return T
   */
  #[@generic(return= 'T')]
  public function max() {
    $return= null;
    foreach ($this->elements as $element) {
      if (null === $return || $element > $return) $return= $element;
    }
    return $return;
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
   * @return void
   */
  public function each($consumer) {
    $inv= Closure::of($consumer);
    foreach ($this->elements as $element) {
      $inv($element);
    }
  }

  /**
   * Returns a new stream with only the first `n` elements
   *
   * @param  int $n
   * @return self<T>
   */
  #[@generic(return= 'self<T>')]
  public function limit($n) {
    return new self(new \LimitIterator($this->getIterator(), 0, $n));
  }

  /**
   * Returns a new stream with only the first `n` elements
   *
   * @param  int $n
   * @return self<T>
   */
  #[@generic(return= 'self<T>')]
  public function skip($n) {
    return new self(new \LimitIterator($this->getIterator(), $n, -1));
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
}