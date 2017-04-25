<?php namespace util\data;

/**
 * A mapper reaches applies a given mapper function to each value an
 * iterator returns and returns its result.
 *
 * @deprecated
 */
abstract class AbstractMapper extends \lang\Object implements \Iterator {
  protected $it, $func, $yieldMap;
  private $current, $key, $valid;

  /**
   * Creates a new Mapper instance
   *
   * @param  php.Mapper $it
   * @param  php.Closure $func
   * @param  bool $yieldMap Whether yield $key => $value should be used to create maps
   */
  public function __construct(\Iterator $it, \Closure $func, $yieldMap= true) {
    $this->it= $it;
    $this->func= $func;
    $this->yieldMap= $yieldMap;
  }

  /** @return var */
  protected abstract function map();

  /** @return void */
  protected function forward() {
    if ($this->valid= $this->it->valid()) {
      $result= $this->map();
      if ($this->yieldMap && $result instanceof \Generator) {
        foreach ($result as $this->key => $this->current) { }
      } else {
        $this->key= $this->it->key();
        $this->current= $result;
      }
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