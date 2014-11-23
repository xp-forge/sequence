<?php namespace util\data;

/**
 * 
 */
class Distribution extends \lang\Object implements \Iterator {
  protected $it;
  protected $workers;
  protected $inv;

  /**
   * Creates a new Generator instance
   *
   * @param  php.Iterator $it
   * @param  util.data.Workers $workers
   */
  public function __construct(\Iterator $it, Workers $workers) {
    $this->it= $it;
    $this->workers= $workers;
    $this->inv= 0;
  }

  protected function enqueue() {
    while ($this->valid() && $this->workers->enqueue($this->it->current())) {
      $this->it->next();
    }
  }

  /** @return void */
  public function rewind() {
    $this->it->rewind();
    $this->enqueue();
  }

  /** @return var */
  public function current() {
    return $this->workers->dequeue();
  }

  /** @return int */
  public function key() {
    return $this->inv++;
  }

  /** @return void */
  public function next() {
    if ($this->workers->pending()) {
      // Nothing
    } else {
      $this->it->next();
      $this->enqueue();
    }
  }

  /** @return bool */
  public function valid() {
    return $this->it->valid();
  }
}