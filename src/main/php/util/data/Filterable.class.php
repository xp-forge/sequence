<?php namespace util\data;

/**
 * A filterable only returns elements which the given accept function
 * returns true for, omitting the others.
 */
class Filterable extends \lang\Object implements \Iterator {
  protected $it;
  protected $accept;

  /**
   * Creates a new Mapper instance
   *
   * @param  php.Mapper $it
   * @param  php.Closure $accept
   */
  public function __construct(\Iterator $it, \Closure $accept) {
    $this->it= $it;
    $this->accept= $accept;
  }

  /** @return void */
  public function rewind() {
    $this->it->rewind();
    while (!$this->accept->__invoke($this->it->current()) && $this->it->valid()) {
      $this->it->next();
    }
  }

  /** @return var */
  public function current() {
    return $this->it->current();
  }

  /** @return var */
  public function key() {
    return $this->it->key();
  }

  /** @return void */
  public function next() {
    do {
      $this->it->next();
    } while (!$this->accept->__invoke($this->it->current()) && $this->it->valid());
  }

  /** @return bool */
  public function valid() {
    return $this->it->valid();
  }
}