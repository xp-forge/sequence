<?php namespace util\data;

use lang\IllegalStateException;

/**
 * Adapter class for wrapping an util.XPIterator instance in an iterator
 * useable by PHP.
 */
class XPIteratorAdapter extends \lang\Object implements \Iterator {
  private $it;
  private $key= -1, $current= null, $valid= false;

  /** @param util.XPIterator $it */
  public function __construct($it) { $this->it= $it; }

  /** @return var */
  public function current() { return $this->current; }

  /** @return var */
  public function key() { return $this->key; }

  /** @return bool */
  public function valid() { return $this->valid; }

  /** @return void */
  public function next() {
    if ($this->valid= $this->it->hasNext()) {
      $this->current= $this->it->next();
      $this->key++;
    }
  }

  /** @return void */
  public function rewind() {
    if ($this->key > -1) {
      throw new IllegalStateException('Cannot rewind iterator');
    } else {
      $this->next();
    }
  }
}