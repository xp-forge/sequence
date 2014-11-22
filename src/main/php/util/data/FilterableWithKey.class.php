<?php namespace util\data;

/**
 * A filterable only returns elements which the given accept function
 * returns true for, omitting the others.
 *
 * @see   php://CallbackFilterIterator but only passes two arguments
 */
class FilterableWithKey extends AbstractFilterable {

  /** @return bool */
  public function accept() {
    return $this->accept->__invoke($this->it->current(), $this->it->key());
  }
}