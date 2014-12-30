<?php namespace util\data;

/**
 * Continuation of a given iteration, that is, rewind() is not called on the
 * underlying iterator.
 *
 * @test  xp://util.data.unittest.ContinuationOfTest
 */
class ContinuationOf extends \lang\Object implements \Iterator {

  /** @param php.Iterator $it */
  public function __construct($it) { $this->it= $it; }

  /** @return var */
  public function current() { return $this->it->current(); }

  /** @return var */
  public function key() { return $this->it->key(); }

  /** @return bool */
  public function valid() { return $this->it->valid(); }

  /** @return void */
  public function next() { $this->it->next(); }

  /** @return void */
  public function rewind() { /* NOOP */ }
}