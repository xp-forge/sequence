<?php namespace util\data\unittest;

use util\data\Window;

class WindowTest extends \unittest\TestCase {
  protected static $fixture= [1, 2, 3, 4, 5];

  /**
   * Returns a window on values with a given skip and stop function using the CUT.
   *
   * @param  var[] $values
   * @param  function<var: bool> $skip
   * @param  function<var: bool> $stop
   * @return var[]
   */
  protected function window($values, $skip, $stop) {
    $result= [];
    foreach (new Window(new \ArrayIterator($values), $skip, $stop) as $val) {
      $result[]= $val;
    }
    return $result;
  }

  #[@test]
  public function works_with_empty_input() {
    $this->assertEquals([], $this->window(
      [],
      function($e) { return false; },
      function($e) { return false; }
    ));
  }

  #[@test]
  public function returns_complete_input_when_both_skip_and_stop_return_false() {
    $this->assertEquals(self::$fixture, $this->window(
      self::$fixture,
      function($e) { return false; },
      function($e) { return false; }
    ));
  }

  #[@test]
  public function does_not_return_elements_skipped() {
    $this->assertEquals([3, 4, 5], $this->window(
      self::$fixture,
      function($e) { return $e < 3; },
      function($e) { return false; }
    ));
  }

  #[@test]
  public function does_not_return_elements_after_stopped() {
    $this->assertEquals([1, 2, 3], $this->window(
      self::$fixture,
      function($e) { return false; },
      function($e) { return $e > 3; }
    ));
  }

  #[@test]
  public function skips_and_stops() {
    $this->assertEquals([3], $this->window(
      self::$fixture,
      function($e) { return $e < 3; },
      function($e) { return $e > 3; }
    ));
  }

  #[@test]
  public function returns_empty_when_stop_returns_true_before_skip_is_true() {
    $this->assertEquals([], $this->window(
      self::$fixture,
      function($e) { return $e < 3; },
      function($e) { return $e > 0; }
    ));
  }

  #[@test]
  public function skip_and_stop_invocations() {
    $invocations= ['skip' => [], 'stop' => []];
    $this->window(
      self::$fixture,
      function($e) use(&$invocations) { $invocations['skip'][]= $e; return $e < 3; },
      function($e) use(&$invocations) { $invocations['stop'][]= $e; return $e > 3; }
    );
    $this->assertEquals(['skip' => [1, 2, 3], 'stop' => [1, 2, 3, 4]], $invocations);
  }

  #[@test]
  public function all_elements_received_if_no_stop_condition() {
    $invocations= [];
    $this->window(
      self::$fixture,
      function($e) { return false; },
      function($e) use(&$invocations) { $invocations[]= $e; return false; }
    );
    $this->assertEquals([1, 2, 3, 4, 5], $invocations);
  }
}