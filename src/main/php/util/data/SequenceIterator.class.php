<?php namespace util\data;

use Traversable, IteratorAggregate;
use util\XPIterator;

/**
 * Iterates over Sequence instances
 *
 * @see  util.data.Sequence#iterator
 * @test util.data.unittest.SequenceIteratorTest
 */
class SequenceIterator implements XPIterator, IteratorAggregate {
  private $it;

  /**
   * Creates a new iterator over a sequence
   *
   * @param  util.data.Sequence $seq
   * @throws util.data.CannotReset If the sequence has been processed
   */
  public function __construct(Sequence $seq) {
    $this->it= $seq->getIterator();
    $this->it->rewind();
  }

  /** Optimizes case when this iterator is wrapped in a PHP iterator */
  public function getIterator(): Traversable {
    return new ContinuationOf($this->it);
  }

  /**
   * Checks whether there are more elements
   *
   * @return bool
   */
  public function hasNext() {
    return $this->it->valid();
  }

  /**
   * Returns next element
   *
   * @return var
   * @throws util.data.NoSuchElement If there are no more elements
   */
  public function next() {
    if ($this->it->valid()) {
      $return= $this->it->current();
      $this->it->next();
      return $return;
    }

    throw new NoSuchElement('No more elements');
  }
}