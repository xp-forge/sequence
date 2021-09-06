<?php namespace util\data;

use Iterator as Base;
use IteratorAggregate, ReturnTypeWillChange;

abstract class Iterator implements Base {
  protected $it;

  /** @param php.Traversable $arg */
  public function __construct($arg) {
    $this->it= $arg instanceof IteratorAggregate ? $arg->getIterator() : $arg;
  }

  /**
   * Rewind
   *
   * @return void
   * @throws util.data.CannotReset
   */
  #[ReturnTypeWillChange]
  public abstract function rewind();

  /** @return string|int */
  #[ReturnTypeWillChange]
  public function key() {
    return $this->it->key();
  }

  /** @return var */
  #[ReturnTypeWillChange]
  public function current() {
    return $this->it->current();
  }

  /** @return bool */
  #[ReturnTypeWillChange]
  public function valid() {
    return $this->it->valid();
  }

  /** @return void */
  #[ReturnTypeWillChange]
  public function next() {
    $this->it->next();
  }
}