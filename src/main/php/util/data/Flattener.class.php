<?php namespace util\data;

/**
 * A flattener treats each element the given iterator returns as a
 * sequence itself and returns each of its elements before continuing
 * with the iterator's next element.
 *
 * @deprecated
 * @test  xp://util.data.unittest.FlattenerTest
 */
class Flattener extends \lang\Object implements \Iterator {
  protected $it;
  protected $seq;

  /**
   * Creates a new Flattener instance
   *
   * @param  php.Iterator $it
   */
  public function __construct(\Iterator $it) {
    $this->it= $it;
  }

  /**
   * Returns the iterator for the next sequence, or NULL if the end of
   * the outer iterator has been reached.
   *
   * @return  php.Iterator
   */
  protected function sequence() {
    while ($this->it->valid()) {
      $value= $this->it->current();
      $seq= $value instanceof Sequence ? $value->getIterator() : Sequence::of($value)->getIterator();
      $seq->rewind();

      if ($seq->valid()) {
        return $seq;
      } else {
        $this->it->next();
      }
    }
    return null;
  }

  /** @return void */
  public function rewind() {
    $this->it->rewind();
    $this->seq= $this->sequence();
  }

  /** @return var */
  public function current() {
    return $this->seq->current();
  }

  /** @return var */
  public function key() {
    return $this->seq->key();
  }

  /** @return void */
  public function next() {
    $this->seq->next();
    if (!$this->seq->valid()) {
      $this->it->next();
      $this->seq= $this->sequence();
    }
  }

  /** @return bool */
  public function valid() {
    return $this->seq && $this->it->valid();
  }
}