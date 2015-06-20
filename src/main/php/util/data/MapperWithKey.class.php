<?php namespace util\data;

/**
 * A mapper reaches applies a given mapper function to each value an
 * iterator returns and returns its result.
 */
class MapperWithKey extends AbstractMapper {

  /** @return var */
  public function map() {
    return $this->func->__invoke($this->it->current(), $this->it->key());
  }
}