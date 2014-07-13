<?php namespace util\data\unittest;

use util\data\Mapper;

class MapperTest extends \unittest\TestCase {

  /**
   * Maps values with a given apply function using the CUT.
   *
   * @param  var[] $values
   * @param  function<var: var> $apply
   * @return var[]
   */
  protected function map($values, $apply) {
    $result= [];
    foreach (new Mapper(new \ArrayIterator($values), $apply) as $val) {
      $result[]= $val;
    }
    return $result;
  }

  #[@test]
  public function works_with_empty_input() {
    $this->assertEquals([], $this->map([], function($e) { return null; }));
  }

  #[@test]
  public function does_not_call_apply_for_empty_input() {
    $this->map([], function($e) {
      throw new \lang\IllegalStateException('Should not have been invoked');
    });
  }

  #[@test]
  public function apply_gets_called_once_for_every_input_element() {
    $values= [1, 2, 3];
    $called= [];
    $this->map($values, function($e) use(&$called) {
      $called[]= $e;
      return null;
    });
    $this->assertEquals($values, $called);
  }

  #[@test]
  public function maps_all_elements() {
    $this->assertEquals([0, 2, 4, 6], $this->map([0, 1, 2, 3], function($e) { return $e * 2; }));
  }
}