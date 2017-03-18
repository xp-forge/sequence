<?php namespace util\data;

/**
 * Continuation of a given iteration, that is, rewind() is not called on the
 * underlying iterator.
 *
 * @test  xp://util.data.unittest.ContinuationOfTest
 */
class ContinuationOf extends Iterator {

  /**
   * Rewind
   *
   * @return void
   * @throws util.data.CannotReset
   */
  public function rewind() {
    // NOOP
  }
}