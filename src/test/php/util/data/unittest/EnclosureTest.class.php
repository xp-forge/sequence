<?php namespace util\data\unittest;

use util\data\Sequence;

class EnclosureTest extends AbstractSequenceTest {

  #[@test]
  public function initially_not_invoked_for_empty_sequence() {
    $initial= null;
    $all= Sequence::$EMPTY->initially(function($e) use(&$initial) { $initial= $e; })->toArray();
    $this->assertEquals([null, []], [$initial, $all]);
  }

  #[@test]
  public function initially_gets_invoked_once() {
    $initial= null;
    $all= Sequence::of([1, 2, 3, 4])->initially(function($e) use(&$initial) { $initial= $e; })->toArray();
    $this->assertEquals([1, [1, 2, 3, 4]], [$initial, $all]);
  }

  #[@test]
  public function initially_gets_invoked_once_with_key() {
    $initial= null;
    $all= Sequence::of(['color' => 'green', 'price' => 12.99])->initially(function($e, $key) use(&$initial) { $initial= [$key => $e]; })->toArray();
    $this->assertEquals([['color' => 'green'], ['green', 12.99]], [$initial, $all]);
  }

  #[@test]
  public function ultimately_not_invoked_for_empty_sequence() {
    $last= null;
    $all= Sequence::$EMPTY->ultimately(function($e) use(&$last) { $last= $e; })->toArray();
    $this->assertEquals([[], null], [$all, $last]);
  }

  #[@test]
  public function ultimately_gets_invoked_once() {
    $last= null;
    $all= Sequence::of([1, 2, 3, 4])->ultimately(function($e) use(&$last) { $last= $e; })->toArray();
    $this->assertEquals([[1, 2, 3, 4], 4], [$all, $last]);
  }

  #[@test]
  public function ultimately_gets_invoked_once_with_key() {
    $last= null;
    $all= Sequence::of(['color' => 'green', 'price' => 12.99])->ultimately(function($e, $key) use(&$last) { $last= [$key => $e]; })->toArray();
    $this->assertEquals([['green', 12.99], ['price' => 12.99]], [$all, $last]);
  }
}