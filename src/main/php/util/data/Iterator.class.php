<?php namespace util\data;

abstract class Iterator implements \Iterator {
  protected $it;

  /** @param php.Traversable $arg */
  public function __construct($arg) {
    $this->it= $arg instanceof \IteratorAggregate ? $arg->getIterator() : $arg;
  }

  /**
   * Rewind
   *
   * @return void
   * @throws util.data.CannotReset
   */
  public abstract function rewind();

  /** @return string|int */
  public function key() {
    return $this->it->key();
  }

  /** @return var */
  public function current() {
    return $this->it->current();
  }

  /** @return bool */
  public function valid() {
    return $this->it->valid();
  }

  /** @return void */
  public function next() {
    $this->it->next();
  }
}