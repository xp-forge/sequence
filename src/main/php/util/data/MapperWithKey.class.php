<?php namespace util\data;

/**
 * A mapper reaches applies a given mapper function to each value an
 * iterator returns and returns its result.
 */
class MapperWithKey extends \lang\Object implements \Iterator {
  protected $it;
  protected $func;

  /**
   * Creates a new Mapper instance
   *
   * @param  php.Mapper $it
   * @param  php.Closure $func
   */
  public function __construct(\Iterator $it, \Closure $func) {
    $this->it= $it;
    $this->func= $func;
  }

  /** @return void */
  public function rewind() {
    $this->it->rewind();
  }

  /** @return var */
  public function current() {
    return $this->func->__invoke($this->it->current(), $this->it->key());
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