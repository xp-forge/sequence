<?php namespace util\data;

use util\NoSuchElementException;

/**
 * Iterates over Sequence instances
 *
 * @see  xp://util.data.Sequence#iterator
 * @test xp://util.data.unittest.SequenceIteratorTest
 */
class SequenceIterator implements \util\XPIterator, \IteratorAggregate {
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

  /**
   * Optimizes case when this iterator is wrapped in a PHP iterator.
   *
   * @return  php.Iterator
   */
  public function getIterator() {
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
   * @throws util.NoSuchElementException If there are no more elements
   */
  public function next() {
    if ($this->it->valid()) {
      $return= $this->it->current();
      $this->it->next();
      return $return;
    }

    throw new NoSuchElementException('No more elements');
  }
}