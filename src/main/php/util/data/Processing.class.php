<?php namespace util\data;

/**
 * Processing
 *
 * @test  xp://util.data.unittest.ProcessingTest
 */
class Processing extends \lang\Object implements \Iterator {
  private $it, $func;
  private $defer= [];

  /**
   * Creates a new Processor instance
   *
   * @param  php.Mapper $it
   * @param  php.Closure $func
   */
  public function __construct(\Iterator $it, \Closure $func) {
    $this->it= $it;
    $this->func= $func;
  }

  private function process($key, $value) {
    $this->key= $key;
    $result= $this->func->__invoke($this, $value);
    if (null === $this->key) {
      return false;
    } else {
      $this->current= null === $result ? $value : $result;
      return true;
    }
  }

  public function defer($value, $key= null) {
    null === $key && $key= $this->key;
    $this->defer[]= function() use($key, $value) {
      $this->key= $key;
      $this->current= $value;
      return true;
    };
    $this->key= null;
  }

  public function drop($value, $key= null) {
    $this->key= null;
  }

  public function retry($value, $key= null) {
    null === $key && $key= $this->key;
    $this->defer[]= function() use($key, $value) {
      return $this->process($key, $value);
    };
    $this->key= null;
  }

  /** @return void */
  private function forward() {
    while ($this->it->valid()) {
      if ($this->process($this->it->key(), $this->it->current())) {
        $this->valid= true;
        return;
      }

      $this->it->next();
    }

    if ($this->valid= !empty($this->defer)) {
      do {
        $handled= array_shift($this->defer);
      } while (!$handled());
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