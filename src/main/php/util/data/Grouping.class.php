<?php namespace util\data;

/**
 * A grouping returns elements in groups of a given size, and is the opposite
 * of flattening a list of grouos.
 *
 * @test  xp://util.data.unittest.SequenceGroupingTest
 */
class Grouping extends \lang\Object implements \Iterator {
  protected $it, $size, $key, $current, $valid;

  /**
   * Creates a new Grouping instance
   *
   * @param  php.Iterator $it
   * @param  int $size
   */
  public function __construct(\Iterator $it, $size) {
    $this->it= $it;
    $this->size= $size;
  }

  /** @return var[] */
  public function group() {
    $this->valid= $this->it->valid();

    for ($group= [], $i= 0; $i < $this->size && $this->it->valid(); $i++) {
      $key= $this->it->key();
      $group[is_int($key) ? $i : $key]= $this->it->current();
      $this->it->next();
    }

    return $group;
  }

  /** @return void */
  public function rewind() {
    $this->it->rewind();
    $this->current= $this->group();
    $this->key= 0;
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
    $this->current= $this->group();
    $this->key++;
  }

  /** @return bool */
  public function valid() {
    return $this->valid;
  }
}
