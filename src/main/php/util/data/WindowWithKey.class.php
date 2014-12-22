<?php namespace util\data;

/**
 * A window is an iterator that skips elements from an underlying
 * iterator that match its "skip" closure, then returns elements
 * until its "stop" closure is reached.
 */
class WindowWithKey extends AbstractWindow {

  /** @return bool */
  public function skip() {
    return $this->skip->__invoke($this->it->current(), $this->it->key());
  }
  /** @return bool */
  public function stop() {
    return $this->stop->__invoke($this->it->current(), $this->it->key());
  }
}