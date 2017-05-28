<?php namespace util\data;

/**
 * Collector
 *
 * @see   xp://util.data.Sequence#collect
 * @test  xp://util.data.unittest.CollectorsTest
 */
class Collector implements ICollector {
  protected $supplier;
  protected $accumulator;
  protected $finisher;

  /**
   * Create a new instance
   *
   * @param  function(): var $supplier
   * @param  (function(var, var): var|function(var, var, var): var) $accumulator
   * @param  function(var): var $finisher
   */
  public function __construct($supplier, $accumulator, $finisher= null) {
    $this->supplier= Functions::$SUPPLY->newInstance($supplier);
    if (Functions::$CONSUME_WITH_KEY->isInstance($accumulator)) {
      $this->accumulator= Functions::$CONSUME_WITH_KEY->cast($accumulator);
    } else {
      $this->accumulator= Functions::$CONSUME->newInstance($accumulator);
    }
    $this->finisher= null === $finisher ? null : Functions::$APPLY->newInstance($finisher);
  }

  /** @return php.Closure */
  public function supplier() { return $this->supplier; }

  /** @return php.Closure */
  public function accumulator() { return $this->accumulator; }

  /** @return php.Closure */
  public function finisher() { return $this->finisher; }
}