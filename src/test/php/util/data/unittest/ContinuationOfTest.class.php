<?php namespace util\data\unittest;

use util\data\ContinuationOf;
use util\data\Sequence;

class ContinuationOfTest extends \unittest\TestCase {

  #[@test, @values('util.data.unittest.Enumerables::validArrays')]
  public function at_beginning($input) {
    $it= Sequence::of($input)->getIterator();
    $it->rewind();

    $this->assertEquals([0 => 1, 1 => 2, 2 => 3], iterator_to_array(new ContinuationOf($it)));
  }

  #[@test, @values('util.data.unittest.Enumerables::validArrays')]
  public function after_first($input) {
    $it= Sequence::of($input)->getIterator();
    $it->rewind();
    $it->next();

    $this->assertEquals([1 => 2, 2 => 3], iterator_to_array(new ContinuationOf($it)));
  }

  #[@test, @values('util.data.unittest.Enumerables::validArrays')]
  public function at_end($input) {
    $it= Sequence::of($input)->getIterator();
    $it->rewind();
    $it->next();
    $it->next();
    $it->next();

    $this->assertEquals([], iterator_to_array(new ContinuationOf($it)));
  }
}