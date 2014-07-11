<?php namespace util\data;

/**
 * Collector
 *
 * @see   xp://util.data.Sequence#collect
 * @test  xp://util.data.unittest.CollectorsTest
 */
class Collector extends \lang\Object implements ICollector {
  protected $supplier;
  protected $accumulator;
  protected $finisher;

  /**
   * Create a new instance
   *
   * @param  function<var, var> $supplier
   * @param  function<var, var> $accumulator
   * @param  function<var, var> $finisher
   */
  public function __construct($supplier, $accumulator, $finisher= null) {
    $this->supplier= $supplier;
    $this->accumulator= $accumulator;
    $this->finisher= $finisher;
  }

  /** @return function<var, var> */
  public function supplier() { return $this->supplier; }

  /** @return function<var, var> */
  public function accumulator() { return $this->accumulator; }

  /** @return function<var, var> */
  public function finisher() { return $this->finisher; }
}