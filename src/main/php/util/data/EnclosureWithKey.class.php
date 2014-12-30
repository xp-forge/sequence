<?php namespace util\data;

/**
 * Enclosure with key
 *
 * @see   xp://util.data.AbstractEnclosure
 * @test  xp://util.data.unittest.EnclosureTest
 */
class EnclosureWithKey extends AbstractEnclosure {

  /** @return void */
  public function initially() {
    $this->initially->__invoke($this->current, $this->key);
  }

  /** @return void */
  public function ultimately() {
    $this->ultimately->__invoke($this->current, $this->key);
  }
}