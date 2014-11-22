<?php namespace util\data;

/**
 * A filterable only returns elements which the given accept function
 * returns true for, omitting the others.
 *
 * @see   php://CallbackFilterIterator but only passes one argument
 * @test  xp://util.data.unittest.FilterableTest
 */
class Filterable extends AbstractFilterable {

  /** @return bool */
  public function accept() {
    return $this->accept->__invoke($this->it->current());
  }
}