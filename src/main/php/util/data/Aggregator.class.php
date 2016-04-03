<?php namespace util\data;

/**
 * An aggregator aggregates all values in an accumulator and optionally
 * invokes a finisher at the end, returning the result into a specified
 * reference.
 */
class Aggregator extends \lang\Object implements \Iterator {
  private $it, $accumulator, $finisher, $result;
  private $current, $key, $valid;

  /**
   * Creates a new Mapper instance
   *
   * @param  php.Mapper $it
   * @param  var $result
   * @param  php.Closure $accumulator
   * @param  php.Closure $finisher
   */
  public function __construct(\Iterator $it, &$result, $accumulator, $finisher= null) {
    $this->it= $it;
    $this->result= &$result;
    $this->accumulator= $accumulator;
    $this->finisher= $finisher;
  }

  /** @return void */
  protected function forward() {
    if ($this->valid= $this->it->valid()) {
      $this->key= $this->it->key();
      $this->current= $this->it->current();
      $this->accumulator->__invoke($this->result, $this->current);
    } else if ($this->finisher) {
      $this->result= $this->finisher->__invoke($this->result);
    }
  }

  /** @return void */
  public function rewind() {
    $this->it->rewind();
    $this->forward();
  }

  /** @return void */
  public function next() {
    $this->it->next();
    $this->forward();
  }

  /** @return var */
  public function current() { return $this->current; }

  /** @return var */
  public function key() { return $this->key; }

  /** @return bool */
  public function valid() { return $this->valid; }
}