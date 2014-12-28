<?php namespace util\data;

/**
 * Enclosure without key
 *
 * @see   xp://util.data.AbstractEnclosure
 * @test  xp://util.data.unittest.EnclosureTest
 */
class Enclosure extends AbstractEnclosure {

  /** @return void */
  public function initially() {
    $this->initially->__invoke($this->current);
  }

  /** @return void */
  public function ultimately() {
    $this->ultimately->__invoke($this->current);
  }
}