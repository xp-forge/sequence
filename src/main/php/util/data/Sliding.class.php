<?php namespace util\data;

/**
 * A sliding returns elements in overlapping groups of a given size.
 *
 * @test  xp://util.data.unittest.SequenceSlidingTest
 */
class Sliding extends \lang\Object implements \Iterator {
  protected $it, $size, $key, $group, $valid;

  /**
   * Creates a new Grouping instance
   *
   * @param  php.Iterator $it
   * @param  int $size
   */
  public function __construct(\Iterator $it, $size) {
    $this->it= $it;
    $this->size= $size;
    $this->group= [];
  }

  /** @return voud */
  public function fill() {
    $this->valid= $this->it->valid();

    for ($i= sizeof($this->group); $i < $this->size && $this->it->valid(); $i++) {
      $key= $this->it->key();
      $this->group[is_int($key) ? $i : $key]= $this->it->current();
      $this->it->next();
    }
  }

  /** @return void */
  public function rewind() {
    $this->it->rewind();
    $this->fill();
    $this->key= 0;
  }

  /** @return var */
  public function current() {
    return $this->group;
  }

  /** @return var */
  public function key() {
    return $this->key;
  }

  /** @return void */
  public function next() {
    array_shift($this->group);
    $this->fill();
    $this->key++;
  }

  /** @return bool */
  public function valid() {
    return $this->valid;
  }
}

