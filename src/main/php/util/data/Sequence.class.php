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
class Sequence implements \lang\Value, \IteratorAggregate {
  public static $EMPTY;

  private $elements;
  private $error= null;

  static function __static() {
    self::$EMPTY= new self([]);
  }

  /** @param var $elements */
  protected function __construct($elements) {
    $this->elements= $elements;
  }

  /** @return util.XPIterator */
  public function iterator() { return new SequenceIterator($this); }

  /** @return iterable */
  public function getIterator() {
    foreach ($this->elements as $key => $element) {
      if ($this->error) throw $this->error;
      yield $key => $element;
    }
  }

  /** @return self */
  public static function empty() { return new self([]); }

  /**
   * Creates a new stream with an enumeration of elements
   *
   * @see    xp://util.data.Enumeration
   * @param  var... $enumerables an iterator, iterable, generator or array
   * @return self
   * @throws lang.IllegalArgumentException if type of elements argument is incorrect
   */
  public static function of(... $enumerables) {
    switch (sizeof($enumerables)) {
      case 1: return new self(Enumeration::of($enumerables[0]));
      case 0: throw new IllegalArgumentException('Expecting at least one argument');
      default:
        $f= function() use($enumerables) {
          foreach ($enumerables as $arg) {
            foreach (Enumeration::of($arg) as $key => $element) {
              yield $key => $element;
            }
          }
        };
        return new self($f());
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
    $closure= Functions::$UNARYOP->newInstance($op);
    $f= function() use($seed, $closure) {
      while (true) { yield $seed; $seed= $closure($seed); }
    };

    return new self($f());
  }

  /**
   * Creates a new stream which uses a given supplier to provide the values
   *
   * @param  function(): var $supplier
   * @return self
   */
  public static function generate($supplier) {
    $closure= Functions::$SUPPLY->newInstance($supplier);
    $f= function() use($closure) {
      while (true) { yield $closure(); }
    };

    return new self($f());
  }

  /**
   * Returns the first element of this stream, or an empty optional
   *
   * @param  util.Filter|function(var): bool $filter An optional filter
   * @return util.data.Optional
   * @throws util.data.CannotReset if streamed and invoked more than once
   */
  public function first($filter= null) {
    $filter && $this->filter($filter);
    foreach ($this->elements as $element) {
      return new Optional($element);
    }
    return Optional::$EMPTY;
  }

  /**
   * Collects all elements in an array
   *
   * @param  function(var): var $map An optional mapper
   * @return var[]
   * @throws lang.IllegalArgumentException if streamed and invoked more than once
   */
  public function toArray($map= null) {
    $map && $this->map($map);
    $return= [];
    foreach ($this->elements as $element) {
      if ($this->error) throw $this->error;
      $return[]= $element;
    }
    return $return;
  }

  /**
   * Collects all elements in a map
   *
   * @param  function(var): var $map An optional mapper
   * @return [:var]
   * @throws lang.IllegalArgumentException if streamed and invoked more than once
   */
  public function toMap($map= null) {
    $map && $this->map($map);
    $return= [];
    foreach ($this->elements as $key => $element) {
      if ($this->error) throw $this->error;
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
      if ($this->error) throw $this->error;
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
      if ($this->error) throw $this->error;
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

    $elements= $this->elements;
    $return= $collector->supplier()->__invoke();
    if (Functions::$CONSUME_WITH_KEY->isInstance($accumulator)) {
      foreach ($this->elements as $key => $element) {
        if ($this->error) throw $this->error;
        $accumulator($return, $element, $key);
      }
    } else {
      foreach ($this->elements as $element) {
        if ($this->error) throw $this->error;
        $accumulator($return, $element);
      }
    }
    return $finisher ? $finisher($return) : $return;
  }

  /**
   * Invokes a given consumer on each element
   *
   * @param  function(var): void $consumer
   * @param  var[] $args Additional args to pass to function
   * @return int The number of elements
   * @throws lang.IllegalArgumentException if streamed and invoked more than once
   */
  public function each($consumer= null, $args= null) {
    $i= 0;
    if (null !== $args) {
      $inv= Functions::$RECV->newInstance($consumer);
      foreach ($this->elements as $element) {
        if ($this->error) throw $this->error;
        $inv($element, ...$args);
        $i++;
      }
    } else if (Functions::$RECV_WITH_KEY->isInstance($consumer)) {
      $inv= Functions::$RECV_WITH_KEY->cast($consumer);
      foreach ($this->elements as $key => $element) {
        if ($this->error) throw $this->error;
        $inv($element, $key);
        $i++;
      }
    } else if (null !== $consumer) {
      $inv= Functions::$RECV->newInstance($consumer);
      foreach ($this->elements as $element) {
        if ($this->error) throw $this->error;
        $inv($element);
        $i++;
      }
    } else {
      foreach ($this->elements as $element) {
        if ($this->error) throw $this->error;
        $i++;
      }
    }
    return $i;
  }

  /**
   * Returns a new stream with only the first `n` elements
   *
   * @param  int|function(var): bool $arg either an integer or a closure
   * @return self
   * @throws lang.IllegalArgumentException
   */
  public function limit($arg) {
    $elements= $this->elements;

    if (is_numeric($arg)) {
      $max= (int)$arg;
      $f= function() use($elements, $max) {
        $i= 0;
        foreach ($elements as $key => $element) {
          if (++$i > $max) break;
          yield $key => $element;
        }
      };
    } else if (Functions::$APPLY_WITH_KEY->isInstance($arg)) {
      $limit= Functions::$APPLY_WITH_KEY->cast($arg);
      $f= function() use($elements, $limit) {
        foreach ($elements as $key => $element) {
          if ($limit($element, $key)) break;
          yield $key => $element;
        }
      };
    } else {
      $limit= Functions::$APPLY->newInstance($arg);
      $f= function() use($elements, $limit) {
        foreach ($elements as $key => $element) {
          if ($limit($element)) break;
          yield $key => $element;
        }
      };
    }
    $this->elements= $f();
    return $this;
  }

  /**
   * Returns a new stream without the first `n` elements
   *
   * @param  int|function(var): bool $arg either an integer or a closure
   * @return self
   * @throws lang.IllegalArgumentException
   */
  public function skip($arg) {
    $elements= $this->elements;

    if (is_numeric($arg)) {
      $max= (int)$arg;
      $f= function() use($elements, $max) {
        $i= 0;
        foreach ($elements as $key => $element) {
          if (++$i > $max) yield $key => $element;
        }
      };
    } else if (Functions::$APPLY_WITH_KEY->isInstance($arg)) {
      $skip= Functions::$APPLY_WITH_KEY->cast($arg);
      $f= function() use($elements, $skip) {
        $skipping= true;
        foreach ($elements as $key => $element) {
          if ($skipping) {
            if ($skip($element, $key)) continue;
            $skipping= false;
          }
          yield $key => $element;
        }
      };
    } else {
      $skip= Functions::$APPLY->newInstance($arg);
      $f= function() use($elements, $skip) {
        $skipping= true;
        foreach ($elements as $key => $element) {
          if ($skipping) {
            if ($skip($element)) continue;
            $skipping= false;
          }
          yield $key => $element;
        }
      };
    }
    $this->elements= $f();
    return $this;
  }

  /**
   * Returns a new stream with elements matching the given predicate
   *
   * @param  util.Filter|function(var): bool $predicate
   * @return self
   * @throws lang.IllegalArgumentException
   */
  public function filter($predicate) {
    $elements= $this->elements;

    if ($predicate instanceof Filter || is('util.Filter<?>', $predicate)) {
      $f= function() use($elements, $predicate) {
        foreach ($elements as $key => $element) {
          try {
            if ($predicate->accept($element)) yield $key => $element;
          } catch (\Throwable $t) {
            $this->error= $t;
          } catch (\Exception $e) {
            $this->error= $e;
          }
        }
      };
    } else if (Functions::$APPLY_WITH_KEY->isInstance($predicate)) {
      $filter= Functions::$APPLY_WITH_KEY->cast($predicate);
      $f= function() use($elements, $filter) {
        foreach ($elements as $key => $element) {
          $keep= true;
          try {
            $keep= $filter($element, $key);
          } catch (\Throwable $t) {
            $this->error= $t;
          } catch (\Exception $e) {
            $this->error= $e;
          }
          $keep && yield $key => $element;
        }
      };
    } else {
      $filter= Functions::$APPLY->newInstance($predicate);
      $f= function() use($elements, $filter) {
        foreach ($elements as $key => $element) {
          $keep= true;
          try {
            $keep= $filter($element);
          } catch (\Throwable $t) {
            $this->error= $t;
          } catch (\Exception $e) {
            $this->error= $e;
          }
          $keep && yield $key => $element;
        }
      };
    }
    $this->elements= $f();
    return $this;
  }

  /**
   * Returns a new stream which maps the given function to each element
   *
   * @param  function(var): var $function
   * @return self
   * @throws lang.IllegalArgumentException
   */
  public function map($function) {
    $elements= $this->elements;

    if (Functions::$APPLY_WITH_KEY->isInstance($function)) {
      $m= Functions::$APPLY_WITH_KEY->cast($function);
      $f= function() use($elements, $m) {
        foreach ($elements as $key => $element) {
          $mapped= null;
          try {
            $mapped= $m($element, $key);
          } catch (\Throwable $t) {
            $this->error= $t;
          } catch (\Exception $e) {
            $this->error= $e;
          }
          if ($mapped instanceof \Generator) {
            foreach ($mapped as $k => $v) { yield $k => $v; }
          } else {
            yield $key => $mapped;
          }
        }
      };
    } else {
      $m= Functions::$APPLY->newInstance($function);
      $f= function() use($elements, $m) {
        foreach ($elements as $key => $element) {
          $mapped= null;
          try {
            $mapped= $m($element);
          } catch (\Throwable $t) {
            $this->error= $t;
          } catch (\Exception $e) {
            $this->error= $e;
          }
          if ($mapped instanceof \Generator) {
            foreach ($mapped as $k => $v) { yield $k => $v; }
          } else {
            yield $key => $mapped;
          }
        }
      };
    }
    $this->elements= $f();
    return $this;
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
    $elements= $this->elements;

    if (null === $function) {
      $f= function() use($elements) {
        foreach ($elements as $element) {
          foreach ($element as $k => $v) { yield $k => $v;  }
        }
      };
    } else if (Functions::$APPLY_WITH_KEY->isInstance($function)) {
      $mapper= Functions::$APPLY_WITH_KEY->cast($function);
      $f= function() use($elements, $mapper) {
        foreach ($elements as $key => $element) {
          foreach ($mapper($element, $key) as $k => $v) { yield $k => $v; }
        }
      };
    } else {
      $mapper= Functions::$APPLY->newInstance($function);
      $f= function() use($elements, $mapper) {
        foreach ($elements as $key => $element) {
          foreach ($mapper($element) as $k => $v) { yield $k => $v; }
        }
      };
    }
    $this->elements= $f();
    return $this;
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
    $elements= $this->elements;

    if (null !== $args) {
      $peek= Functions::$RECV->newInstance($action);
      $f= function() use($elements, $peek, $args) {
        foreach ($elements as $key => $element) {
          try {
            $peek($element, ...$args);
          } catch (\Throwable $t) {
            $this->error= $t;
          } catch (\Exception $e) {
            $this->error= $e;
          }
          yield $key => $element;
        }
      };
    } else if (Functions::$RECV_WITH_KEY->isInstance($action)) {
      $peek= Functions::$RECV_WITH_KEY->cast($action);
      $f= function() use($elements, $peek) {
        foreach ($elements as $key => $element) {
          try {
            $peek($element, $key);
          } catch (\Throwable $t) {
            $this->error= $t;
          } catch (\Exception $e) {
            $this->error= $e;
          }
          yield $key => $element;
        }
      };
    } else {
      $peek= Functions::$RECV->newInstance($action);
      $f= function() use($elements, $peek) {
        foreach ($elements as $key => $element) {
          try {
            $peek($element);
          } catch (\Throwable $t) {
            $this->error= $t;
          } catch (\Exception $e) {
            $this->error= $e;
          }
          yield $key => $element;
        }
      };
    }
    $this->elements= $f();
    return $this;
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
    $accumulator= $collector->accumulator();
    $finisher= $collector->finisher();

    $elements= $this->elements;
    $return= $collector->supplier()->__invoke();
    if (Functions::$CONSUME_WITH_KEY->isInstance($accumulator)) {
      $f= function() use($elements, &$return, $accumulator, $finisher) {
        foreach ($elements as $key => $element) {
          try {
            $accumulator($return, $element, $key);
          } catch (\Throwable $t) {
            $this->error= $t;
          } catch (\Exception $e) {
            $this->error= $e;
          }
          yield $key => $element;
        }
        $finisher && $return= $finisher($return);
      };
    } else {
      $f= function() use($elements, &$return, $accumulator, $finisher) {
        foreach ($elements as $key => $element) {
          try {
            $accumulator($return, $element);
          } catch (\Throwable $t) {
            $this->error= $t;
          } catch (\Exception $e) {
            $this->error= $e;
          }
          yield $key => $element;
        }
        $finisher && $return= $finisher($return);
      };
    }
    $this->elements= $f();
    return $this;
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
    $elements= $this->elements;
    $f= function() use($elements, &$count) {
      foreach ($elements as $key => $element) {
        $count++;
        yield $key => $element;
      }
    };
    $this->elements= $f();
    return $this;
  }

  /**
   * Returns a stream with distinct elements
   *
   * @param  function(var): var $function - if omitted, `util.Objects::hashOf()` is used
   * @return self
   */
  public function distinct($function= null) {
    $hash= Functions::$APPLY->newInstance($function ?: 'util.Objects::hashOf');
    $elements= $this->elements;
    $f= function() use($elements, $hash) {
      $set= [];
      foreach ($elements as $e) {
        $h= $hash($e);
        if (!isset($set[$h])) {
          $set[$h]= true;
          yield $e;
        }
      }
    };
    $this->elements= $f();
    return $this;
  }

  /**
   * Catches an exception
   *
   * @param  function(lang.Throwable): var $handler
   * @return self
   */
  public function catch($handler) {
    $elements= $this->elements;
    $f= function() use($elements, $handler) {
      foreach ($elements as $key => $element) {
        if ($this->error) {
          $result= $handler($this->error);
          $this->error= null;
          if ($result instanceof \Generator) {
            foreach ($result as $k => $v) { yield $k => $v; }
          }
        } else {
          yield $key => $element;
        }
      }
    };
    $this->elements= $f();
    return $this;
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
   * Compares this optional to another given value
   *
   * @param  var $value
   * @return int
   */
  public function compareTo($value) {
    return $value instanceof self ? Objects::compare($this->elements, $value->elements) : 1;
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