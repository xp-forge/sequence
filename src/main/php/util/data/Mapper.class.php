<?php namespace util\data;

/**
 * A mapper reaches applies a given mapper function to each value an
 * iterator returns and returns its result.
 *
 * @test  xp://util.data.unittest.MapperTest
 */
class Mapper extends AbstractMapper {

  /** @return var */
  public function current() {
    return $this->func->__invoke($this->it->current());
  }
}