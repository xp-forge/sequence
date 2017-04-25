<?php namespace util\data;

/**
 * A filterable only returns elements which the given accept function
 * returns true for, omitting the others.
 *
 * @deprecated
 */
abstract class AbstractFilterable extends \lang\Object implements \Iterator {
  protected $it;
  protected $accept;

  /**
   * Creates a new filterable instance
   *
   * @param  php.Iterator $it
   * @param  php.Closure $accept
   */
  public function __construct(\Iterator $it, \Closure $accept) {
    $this->it= $it;
    $this->accept= $accept;
  }

  /**
   * Accepts current element. Implementations access the `it` and `accept`
   * member variables directly.
   *
   * @return bool
   */
  public abstract function accept();

  /** @return void */
  public function rewind() {
    $this->it->rewind();
    while ($this->it->valid() && !$this->accept()) {
      $this->it->next();
    }
  }

  /** @return var */
  public function current() { return $this->it->current(); }

  /** @return var */
  public function key() { return $this->it->key(); }

  /** @return void */
  public function next() {
    do {
      $this->it->next();
    } while ($this->it->valid() && !$this->accept());
  }

  /** @return bool */
  public function valid() { return $this->it->valid(); }
}