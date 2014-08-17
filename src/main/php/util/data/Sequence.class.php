<?php namespace util\data;

use util\Objects;
use util\Comparator;
use util\Filter;
use lang\IllegalArgumentException;
use lang\IllegalStateException;

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
   * Invoke terminal operation
   *
   * @param  function(): R $operation
   * @return R
   */
  protected function terminal($operation) {
    static $message= 'Underlying value is streamed and cannot be processed more than once';

    try {
      return $operation();
    } catch (\lang\IllegalStateException $e) {
      throw new IllegalStateException($message, $e);
    } catch (\lang\XPException $e) {
      throw $e;
    } catch (\Exception $e) {
      throw new IllegalStateException($message.':'.$e->getMessage());
    }
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
   * @param  function(R): R $op
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
   * @param  function(): R $supplier
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
   * Returns the first element of this stream, or an empty optional
   *
   * @return util.data.Optional<T>
   * @throws lang.IllegalArgumentException if streamed and invoked more than once
   */
  #[@generic(return= 'T')]
  public function first() {
    return $this->terminal(function() {
      $gen= $this->elements instanceof \Generator;
      if ($gen && isset($this->elements->closed)) {
        throw new IllegalStateException('Generator closed');
      }
      foreach ($this->elements as $element) {
        $gen && $this->elements->closed= true;
        return Optional::of($element);
      }
      return Optional::$EMPTY;
    });
  }

  /**
   * Collects all elements in an array
   *
   * @return T[]
   * @throws lang.IllegalArgumentException if streamed and invoked more than once
   */
  #[@generic(return= 'T[]')]
  public function toArray() {
    return $this->terminal(function() {
      $return= [];
      foreach ($this->elements as $element) {
        $return[]= $element;
      }
      return $return;
    });
  }

  /**
   * Counts all elements
   *
   * @return int
   * @throws lang.IllegalArgumentException if streamed and invoked more than once
   */
  public function count() {
    return $this->terminal(function() {
      $return= 0;
      foreach ($this->elements as $element) {
        $return++;
      }
      return $return;
    });
  }

  /**
   * Returns the sum of all elements
   *
   * @return T
   * @throws lang.IllegalArgumentException if streamed and invoked more than once
   */
  #[@generic(return= 'T')]
  public function sum() {
    return $this->terminal(function() {
      $return= 0;
      foreach ($this->elements as $element) {
        $return+= $element;
      }
      return $return;
    });
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
   * @throws lang.IllegalArgumentException if streamed and invoked more than once
   */
  #[@generic(return= 'T')]
  public function min($comparator= null) {
    return $this->terminal(function() use($comparator) {
      if (null === $comparator) {
        $return= null;
        foreach ($this->elements as $element) {
          if (null === $return || $element < $return) $return= $element;
        }
        return $return;
      }
      return $this->select($comparator, -1);
    });
  }

  /**
   * Returns the largest element. Optimized for the case when no comparator is 
   * given, using the `>` operator.
   *
   * @param  var $comparator default NULL Either a Comparator or a closure to compare.
   * @return T
   * @throws lang.IllegalArgumentException if streamed and invoked more than once
   */
  #[@generic(return= 'T')]
  public function max($comparator= null) {
    return $this->terminal(function() use($comparator) {
      if (null === $comparator) {
        $return= null;
        foreach ($this->elements as $element) {
          if (null === $return || $element > $return) $return= $element;
        }
        return $return;
      }
      return $this->select($comparator, +1);
    });
  }

  /**
   * Performs a reduction on the elements of this stream, using the provided identity
   * value and an associative accumulation function, and returns the reduced value.
   *
   * @param  R $identity
   * @param  function(R, R): R $function
   * @return R
   * @throws lang.IllegalArgumentException if streamed and invoked more than once
   */
  public function reduce($identity, $accumulator) {
    return $this->terminal(function() use($identity, $accumulator) {
      $closure= Closure::of($accumulator);
      $return= $identity;
      foreach ($this->elements as $element) {
        $return= $closure($return, $element);
      }
      return $return;
    });
  }

  /**
   * Performs a mutable reduction operation on the elements of this stream.
   *
   * @param  util.data.ICollector
   * @return R
   * @throws lang.IllegalArgumentException if streamed and invoked more than once
   */
  public function collect(ICollector $collector) {
    return $this->terminal(function() use($collector) {
      $accumulator= $collector->accumulator();
      $finisher= $collector->finisher();

      $return= $collector->supplier()->__invoke();
      foreach ($this->elements as $element) {
        $accumulator($return, $element);
      }

      return $finisher ? $finisher($return) : $return;
    });
  }

  /**
   * Invokes a given consumer on each element
   *
   * @param  functionT $function
   * @return int The number of elements
   * @throws lang.IllegalArgumentException if streamed and invoked more than once
   */
  public function each($consumer) {
    return $this->terminal(function() use($consumer) {
      $inv= Closure::of($consumer);
      $i= 0;
      foreach ($this->elements as $element) {
        $inv($element);
        $i++;
      }
      return $i;
    });
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
   * Returns a new stream with elements matching the given predicate
   *
   * @param  var $predicate either a util.Filter instance or a function
   * @return self<T>
   */
  #[@generic(return= 'self<T>')]
  public function filter($predicate) {
    if (is('util.Filter<?>', $predicate) || $predicate instanceof Filter) {
      $f= Closure::of([$predicate, 'accept']);
    } else {
      $f= Closure::of($predicate);
    }
    return new self(new Filterable($this->getIterator(), $f));
  }

  /**
   * Returns a new stream which maps the given function to each element
   *
   * @param  functionT: R $function
   * @return self<R>
   */
  #[@generic(return= 'self<R>')]
  public function map($function) {
    return new self(new Mapper($this->getIterator(), Closure::of($function)));
  }

  /**
   * Returns a new stream which flattens, mapping the given function to each
   * element.
   *
   * @param  functionT: R $function - if omitted, the identity function is used.
   * @return self<R>
   */
  #[@generic(return= 'self<R>')]
  public function flatten($function= null) {
    if (null === $function) {
      $it= $this->getIterator();
    } else {
      $it= new Mapper($this->getIterator(), Closure::of($function));
    }
    return new self(new Flattener($it));
  }

  /**
   * Returns a new stream which additionally calls the given function for 
   * each element it consumes. Use this e.g. for debugging purposes.
   *
   * @param  functionT: R $action
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