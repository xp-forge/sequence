<?php namespace util\data\unittest;

use util\data\Filterable;

class FilterableTest extends \unittest\TestCase {

  /**
   * Filters values with a given accept function using the CUT.
   *
   * @param  var[] $values
   * @param  function(var): bool $accept
   * @return var[]
   */
  protected function filter($values, $accept) {
    $result= [];
    foreach (new Filterable(new \ArrayIterator($values), $accept) as $val) {
      $result[]= $val;
    }
    return $result;
  }

  #[@test]
  public function works_with_empty_input() {
    $this->assertEquals([], $this->filter([], function($e) { return true; }));
  }

  #[@test]
  public function does_not_call_accept_for_empty_input() {
    $this->filter([], function($e) {
      throw new \lang\IllegalStateException('Should not have been invoked');
    });
  }

  #[@test, @values([true, false])]
  public function accept_gets_called_once_for_every_input_element($filtering) {
    $values= [1, 2, 3];
    $called= [];
    $this->filter($values, function($e) use(&$called, $filtering) {
      $called[]= $e;
      return $filtering;
    });
    $this->assertEquals($values, $called);
  }

  #[@test, @values([
  #  [[1], 'only one element'],
  #  [[1, 2, 3], 'multiple']
  #])]
  public function returns_all_elements($values, $desc) {
    $this->assertEquals($values, $this->filter($values, function($e) { return true; }));
  }

  #[@test, @values([
  #  [[1, 2, 3], 'only one element'],
  #  [[-1, 0, 1, 2, 3], 'multiple']
  #])]
  public function filters_first($values, $desc) {
    $this->assertEquals([2, 3], $this->filter($values, function($e) { return $e > 1; }), $desc);
  }

  #[@test, @values([
  #  [[1, 2, 3], 'only one element'],
  #  [[1, 2, 3, 4, 5], 'multiple']
  #])]
  public function filters_last($values, $desc) {
    $this->assertEquals([1, 2], $this->filter($values, function($e) { return $e < 3; }), $desc);
  }

  #[@test, @values([
  #  [[1, 2, 3], 'only one element'],
  #  [[1, 2, 4, 6, 3], 'multiple']
  #])]
  public function filters_inbetween($values, $desc) {
    $this->assertEquals([1, 3], $this->filter($values, function($e) { return $e % 2 > 0; }), $desc);
  }
}