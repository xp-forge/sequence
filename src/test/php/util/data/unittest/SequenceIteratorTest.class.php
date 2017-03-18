<?php namespace util\data\unittest;

use util\data\Sequence;
use util\XPIterator;
use util\NoSuchElementException;
use util\data\CannotReset;

class SequenceIteratorTest extends AbstractSequenceTest {

  /**
   * Collects all values in an iterator in an array
   *
   * @param  util.XPIterator $iterator
   * @return var[]
   */
  protected function iterated(XPIterator $iterator) {
    $elements= [];
    while ($iterator->hasNext()) {
      $elements[]= $iterator->next();
    }
    return $elements;
  }

  #[@test]
  public function hasNext() {
    $this->assertTrue(Sequence::of([1])->iterator()->hasNext());
  }

  #[@test]
  public function next() {
    $this->assertEquals(1, Sequence::of([1])->iterator()->next());
  }

  #[@test]
  public function hasNext_returns_false_when_at_end_of_sequence() {
    $this->assertFalse(Sequence::$EMPTY->iterator()->hasNext());
  }

  #[@test, @expect(NoSuchElementException::class)]
  public function next_throws_exception_when_at_end_of_sequence() {
    Sequence::$EMPTY->iterator()->next();
  }

  #[@test, @values('util.data.unittest.Enumerables::validArrays')]
  public function iterator($input) {
    $this->assertEquals([1, 2, 3], $this->iterated(Sequence::of($input)->iterator()));
  }

  #[@test, @values('util.data.unittest.Enumerables::fixedArrays')]
  public function may_iterate_sequence_based_on_a_fixed_enumerable_more_than_once($input) {
    $seq= Sequence::of($input);
    $this->iterated($seq->iterator());
    $this->iterated($seq->iterator());
  }

  #[@test, @values('util.data.unittest.Enumerables::streamedArrays')]
  public function cannot_iterate_sequence_based_on_a_streamed_enumerable_more_than_once($input) {
    $seq= Sequence::of($input);
    $this->iterated($seq->iterator());
    try {
      $this->iterated($seq->iterator());
      $this->fail('No exception raised', null, 'util.data.CannotReset');
    } catch (CannotReset $expected) {
      // OK
    }
  }

  #[@test, @values('util.data.unittest.Enumerables::validArrays')]
  public function sequence_of_iterator($input) {
    $this->assertSequence([1, 2, 3], Sequence::of(Sequence::of($input)->iterator()));
  }
}