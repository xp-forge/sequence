<?php namespace util\data;

use util\NoSuchElementException;
use lang\IllegalStateException;

/**
 * Iterates over Sequence instances
 *
 * @see  xp://util.data.Sequence#iterator
 * @test xp://util.data.unittest.SequenceIteratorTest
 */
class SequenceIterator extends \lang\Object implements \util\XPIterator {
  private $it;

  /**
   * Creates a new iterator over a sequence
   *
   * @param  util.data.Sequence $seq
   * @throws lang.IllegalStateException If the sequence has been processed
   */
  public function __construct(Sequence $seq) {
    $this->it= $seq->getIterator();
    try {
      $this->it->rewind();
    } catch (\Exception $e) {
      throw new IllegalStateException($e->getMessage());
    }
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