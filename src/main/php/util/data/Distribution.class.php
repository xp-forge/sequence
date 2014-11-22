<?php namespace util\data;

/**
 * 
 */
class Distribution extends \lang\Object implements \Iterator {
  protected $it;
  protected $workers;
  protected $worker;

  /**
   * Creates a new Generator instance
   *
   * @param  php.Iterator $it
   * @param  var[] $workers
   */
  public function __construct(\Iterator $it, array $workers) {
    $this->it= $it;
    $this->workers= $workers;
  }

  /** @return void */
  public function rewind() {
    $this->worker= 0;
    $this->it->rewind();
  }

  /** @return var */
  public function current() {
    return $this->workers[$this->worker]->__invoke($this->it->current());
  }

  /** @return int */
  public function key() {
    return $this->it->key();
  }

  /** @return void */
  public function next() {
    if (++$this->worker >= sizeof($this->workers)) {
      $this->worker= 0;
    }

    $this->it->next();
  }

  /** @return bool */
  public function valid() {
    return $this->it->valid();
  }
}