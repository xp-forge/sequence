<?php namespace util\data;

/**
 * Traversable of a given iteration wrapping exceptions from rewind() in
 * a util.data.CannotReset exceptions to make it distinguishable from other
 * exceptions.
 *
 * @test  xp://util.data.unittest.TraversalOfTest
 */
class TraversalOf extends Iterator {

  /**
   * Rewind. Rethrows PHP7's Throwable base class.
   *
   * @return void
   * @throws util.data.CannotReset
   */
  public function rewind() {
    try {
      $this->it->rewind();
    } catch (\Throwable $e) {
      throw new CannotReset($e->getMessage(), $e);
    }
  }
}