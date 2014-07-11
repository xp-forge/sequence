<?php namespace util\data;

/**
 * A mapper reaches applies a given mapper function to each value an
 * iterator returns and returns its result.
 */
#[@generic(self= 'T')]
class Mapper extends \lang\Object implements \Iterator {
  protected $it;
  protected $func;

  /**
   * Creates a new Mapper instance
   *
   * @param  php.Iterator $it
   * @param  function<T> $func
   */
  public function __construct(\Iterator $it, callable $func) {
    $this->it= $it;
    $this->func= $func;
  }

  /** @return void */
  public function rewind() {
    $this->it->rewind();
  }

  /** @return T */
  public function current() {
    $f= $this->func;
    return $f($this->it->current());
  }

  /** @return var */
  public function key() {
    return $this->it->key();
  }

  /** @return void */
  public function next() {
    $this->it->next();
  }

  /** @return bool */
  public function valid() {
    return $this->it->valid();
  }
}