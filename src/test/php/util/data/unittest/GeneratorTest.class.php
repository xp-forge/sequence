<?php namespace util\data\unittest;

use util\data\Generator;

class GeneratorTest extends \unittest\TestCase {

  /**
   * Generates values with a given limit using the CUT.
   *
   * @param  function<var> $seed
   * @param  function<var> $func
   * @param  int $limit
   * @return var[]
   */
  protected function generate($seed, $func, $limit) {
    $result= [];
    foreach (new Generator($seed, $func) as $i => $val) {
      if ($i >= $limit) break;
      $result[]= $val;
    }
    return $result;
  }

  #[@test]
  public function initial_and_following() {
    $this->assertEquals(['initial', 'following', 'following'], $this->generate(
      function() { return 'initial'; },
      function() { return 'following'; },
      3
    ));
  }

  #[@test]
  public function incrementing() {
    $i= 0;
    $this->assertEquals([0, 2, 4, 6, 8], $this->generate(
      function() { return 0; },
      function() use(&$i) { $i+= 2; return $i; },
      5
    ));
  }
}