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
   * @param  function(var): var $supplier
   * @param  function(var): var $accumulator
   * @param  function(var): var $finisher
   */
  public function __construct($supplier, $accumulator, $finisher= null) {
    $this->supplier= Closure::of($supplier);
    $this->accumulator= Closure::of($accumulator);
    $this->finisher= null === $finisher ? null : Closure::of($finisher);
  }

  /** @return php.Closure */
  public function supplier() { return $this->supplier; }

  /** @return php.Closure */
  public function accumulator() { return $this->accumulator; }

  /** @return php.Closure */
  public function finisher() { return $this->finisher; }
}