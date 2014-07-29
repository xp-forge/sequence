<?php namespace util\data;

/**
 * A flattener treats each element the given iterator returns as a
 * sequence itself and returns each of its elements before continuing
 * with the iterator's next element.
 *
 * @test  xp://util.data.unittest.FlattenerTest
 */
class Flattener extends \lang\Object implements \Iterator {
  protected $it;
  protected $seq;
  protected $func;

  /**
   * Creates a new Flattener instance
   *
   * @param  php.Iterator $it
   * @param  php.Closure $func
   */
  public function __construct(\Iterator $it, \Closure $func= null) {
    $this->it= $it;
    $this->func= $func;
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
      if (null !== $value) {
        if ($value instanceof Sequence) {
          $seq= $value->getIterator();
        } else {
          $seq= Sequence::of($value)->getIterator();
        }
        $seq->rewind();
        if ($seq->valid()) return $seq;
      }
      $this->it->next();
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