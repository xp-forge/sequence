<?php namespace util\data;

/**
 * A window is an iterator that skips elements from an underlying
 * iterator that match its "skip" closure, then returns elements
 * until its "stop" closure is reached.
 *
 * @see   php://LimitIterator but uses closures and not just offsets
 * @test  xp://util.data.unittest.WindowTest
 */
class Window extends \lang\Object implements \Iterator {
  protected $it;
  protected $skip;
  protected $stop;
  protected $valid;

  /**
   * Creates a new Mapper instance
   *
   * @param  php.Iterator $it
   * @param  php.Closure $skip
   * @param  php.Closure $stop
   */
  public function __construct(\Iterator $it, \Closure $skip, \Closure $stop) {
    $this->it= $it;
    $this->skip= $skip;
    $this->stop= $stop;
  }

  /** @return void */
  public function rewind() {
    $this->it->rewind();
    $this->valid= true;
    while ($this->valid()) {
      $current= $this->it->current();
      $skip= $this->skip->__invoke($current);
      if ($this->stop->__invoke($current)) {
        $this->valid= false;
      } else if ($skip) {
        $this->it->next();
        continue;
      }
      break;
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
    $this->it->next();
    if ($this->stop->__invoke($this->it->current())) {
      $this->valid= false;
    }
  }

  /** @return bool */
  public function valid() {
    return $this->valid && $this->it->valid();
  }
}