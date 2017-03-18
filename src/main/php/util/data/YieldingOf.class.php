<?php namespace util\data;

/**
 * Special case of traversal wrapper geared towards generators, where
 * rewind() does not fail until the generator was completely yielded.
 *
 * @test  xp://util.data.unittest.YieldingOfTest
 */
class YieldingOf extends Iterator {
  private $started= false;

  /**
   * Rewind
   *
   * @return void
   * @throws util.data.CannotReset
   */
  public function rewind() {
    if ($this->started) {
      throw new CannotReset('Yielding from generator previously started, cannot rewind');
    }

    $this->it->rewind();
    $this->started= true;
  }
}