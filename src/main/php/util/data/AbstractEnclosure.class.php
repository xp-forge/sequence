<?php namespace util\data;

/**
 * An enclosure calls the "initially" method for the first element and
 * the "ultimately" element for the last one.
 */
abstract class AbstractEnclosure extends \lang\Object implements \Iterator {
  private $it, $valid;
  protected $initially, $ultimately, $key, $current;

  /**
   * Creates a new Enclosure instance
   *
   * @param  php.Mapper $it
   * @param  php.Closure $initially
   * @param  php.Closure $ultimately
   */
  public function __construct(\Iterator $it, \Closure $initially= null, \Closure $ultimately= null) {
    $this->it= $it;
    $this->initially= $initially;
    $this->ultimately= $ultimately;
  }

  /**
   * Invoked once for first element. Implementations access the `key`,
   * `current` and `initially` member variables directly.
   *
   * @return void
   */
  public abstract function initially();

  /**
   * Invoked once for last element. Implementations access the `key`,
   * `current` and `initially` member variables directly.
   *
   * @return void
   */
  public abstract function ultimately();

  /** @return void */
  public function rewind() {
    $this->it->rewind();
    if ($this->valid= $this->it->valid()) {
      $this->key= $this->it->key();
      $this->current= $this->it->current();
      $this->initially && $this->initially();
    }
  }

  /** @return var */
  public function current() {
    return $this->current;
  }

  /** @return var */
  public function key() {
    return $this->key;
  }

  /** @return void */
  public function next() {
    $this->it->next();
    if ($this->it->valid()) {
      $this->key= $this->it->key();
      $this->current= $this->it->current();
    } else {
      $this->valid= false;
      $this->ultimately && $this->ultimately();
    }
  }

  /** @return bool */
  public function valid() {
    return $this->valid;
  }
}