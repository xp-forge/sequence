<?php namespace util\data;

/**
 * Traversable of a given iteration wrapping exceptions from rewind() in
 * a util.data.CannotReset exceptions to make it distinguishable from other
 * exceptions.
 */
class TraversalOf extends Iterator {

  /**
   * Rewind
   *
   * @return void
   * @throws util.data.CannotReset
   */
  public function rewind() {
    try {
      $this->it->rewind();
    } catch (\Throwable $e) {   // PHP7
      throw new CannotReset($e->getMessage(), $e);
    } catch (\Exception $e) {   // PHP5
      throw new CannotReset($e->getMessage(), $e);
    }
  }
}