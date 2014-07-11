<?php namespace util\data;

/**
 * A generator produces an infinite sequence of data, like for example
 * `/dev/urandom` on Unix-like operating systems.
 */
class Generator extends \lang\Object implements \Iterator {
  protected $seed;
  protected $func;
  protected $result;
  protected $inv;

  /**
   * Creates a new Generator instance
   *
   * @param  function<T> $seed
   * @param  function<T> $func
   */
  public function __construct(callable $seed, callable $func) {
    $this->seed= $seed;
    $this->func= $func;
  }

  /** @return void */
  public function rewind() {
    $this->inv= 0;
    $f= $this->seed;
    $this->result= $f();
  }

  /** @return var */
  public function current() {
    return $this->result;
  }

  /** @return int */
  public function key() {
    return $this->inv++;
  }

  /** @return void */
  public function next() {
    $f= $this->func;
    $this->result= $f();
  }

  /** @return bool */
  public function valid() {
    return true;
  }
}