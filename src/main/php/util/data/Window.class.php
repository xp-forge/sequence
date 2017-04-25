<?php namespace util\data;

/**
 * A window is an iterator that skips elements from an underlying
 * iterator that match its "skip" closure, then returns elements
 * until its "stop" closure is reached.
 *
 * @deprecated
 * @see   php://LimitIterator but uses closures and not just offsets
 * @test  xp://util.data.unittest.WindowTest
 */
class Window extends AbstractWindow {

  /** @return bool */
  public function skip() {
    return $this->skip->__invoke($this->it->current());
  }

  /** @return bool */
  public function stop() {
    return $this->stop->__invoke($this->it->current());
  }
}