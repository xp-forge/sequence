<?php namespace util\data;

use lang\IllegalArgumentException;
use util\XPIterator;

/**
 * Represents a list of iterators
 *
 * @deprecated Use Sequence::of() instead of Sequence::concat()
 * @see   php://AppendIterator
 * @test  xp://util.data.unittest.SequenceResultSetTest
 */
class Iterators extends \lang\Object implements \Iterator {
  protected $it, $sources, $source;

  /**
   * Creates a new Iterators instance
   *
   * @param  var $sources
   */
  public function __construct(array $sources) {
    $this->sources= $sources;
  }

  /** @return php.Iterator */
  protected function nextIterator() {
    if ($this->source >= sizeof($this->sources)) return null;
    $src= $this->sources[$this->source];

    // Evaluate lazy supplier functions
    if ($src instanceof \Closure) {
      $src= $src();
    }

    if ($src instanceof \Iterator) {
      $it= $src;
    } else if ($src instanceof \Traversable) {
      $it= new \IteratorIterator($src);
    } else if ($src instanceof XPIterator) {
      $it= new XPIteratorAdapter($src);
    } else if (is_array($src)) {
      $it= new \ArrayIterator($src);
    } else if (null === $src) {
      $it= new \ArrayIterator([]);
    } else {
      throw new IllegalArgumentException('Expecting either an iterator, iterable, an array or NULL');
    }

    $this->source++;
    $it->rewind();
    return $it;
  }

  /** @return void */
  public function rewind() {
    $this->source= 0;
    do {
      $this->it= $this->nextIterator();
    } while ($this->it && !$this->it->valid());
  }

  /** @return var */
  public function current() {
    return $this->it->current();
  }

  /** @return var */
  public function key() {
    return $this->it->key();
  }

  /** @return void */
  public function next() {
    $this->it->next();
    if (!$this->it->valid()) {
      $this->it= $this->nextIterator();
    }
  }

  /** @return bool */
  public function valid() {
    return $this->it && $this->it->valid();
  }
}