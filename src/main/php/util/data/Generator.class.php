<?php namespace util\data;

/**
 * A generator produces an infinite sequence of data, like for example
 * `/dev/urandom` on Unix-like operating systems.
 *
 * @deprecated
 * @test  xp://util.data.unittest.GeneratorTest
 */
class Generator extends \lang\Object implements \Iterator {
  protected $seed;
  protected $func;
  protected $result;
  protected $inv;

  /**
   * Creates a new Generator instance
   *
   * @param  php.Closure $seed
   * @param  php.Closure $func
   */
  public function __construct(\Closure $seed, \Closure $func) {
    $this->seed= $seed;
    $this->func= $func;
  }

  /** @return void */
  public function rewind() {
    $this->inv= 0;
    $this->result= $this->seed->__invoke();
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
    $this->result= $this->func->__invoke();
  }

  /** @return bool */
  public function valid() {
    return true;
  }
}