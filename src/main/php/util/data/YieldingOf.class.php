<?php namespace util\data;

/**
 * Special case of traversal wrapper geared towards generators, where
 * rewind() does not fail until the generator was completely yielded.
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

    try {
      $this->it->rewind();
      $this->started= true;
    } catch (\Throwable $e) {   // PHP7
      throw new CannotReset($e->getMessage(), $e);
    } catch (\Exception $e) {   // PHP5
      throw new CannotReset($e->getMessage(), $e);
    }
  }
}