<?php namespace util\data\unittest;

use util\data\LocalWorkerProcess;

class LocalWorkerProcessTest extends \unittest\TestCase {
  private static $worker;

  #[@beforeClass]
  public static function startWorker() {
    self::$worker= new LocalWorkerProcess('util.data.unittest.Worker', [3]);
  }

  #[@afterClass]
  public static function stopWorker() {
    self::$worker->shutdown();
  }

  #[@test]
  public function pass_and_result_roundtrip() {
    self::$worker->pass(1);
    $this->assertEquals(3, self::$worker->result());
  }

  #[@test]
  public function elements_are_processed_consecutively() {
    self::$worker->pass(1);
    $this->assertEquals(3, self::$worker->result());
    self::$worker->pass(2);
    $this->assertEquals(6, self::$worker->result());
  }

  #[@test, @values([
  #  [[1, 2]],
  #  [[1, 2, 3]],
  #  [[1, 2, 3, 4]]
  #])]
  public function elements_can_be_processed_in_batches($values) {
    foreach ($values as $value) {
      self::$worker->pass($value);
    }

    $recv= [];
    foreach ($values as $value) {
      $recv[]= self::$worker->result() / 3;
    }

    $this->assertEquals($values, $recv);
  }
}