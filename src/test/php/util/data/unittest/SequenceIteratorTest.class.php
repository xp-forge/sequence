<?php namespace util\data\unittest;

use unittest\{Assert, Expect, Test, Values};
use util\XPIterator;
use util\data\{CannotReset, Sequence, NoSuchElement};

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

  #[Test]
  public function hasNext() {
    Assert::true(Sequence::of([1])->iterator()->hasNext());
  }

  #[Test]
  public function next() {
    Assert::equals(1, Sequence::of([1])->iterator()->next());
  }

  #[Test]
  public function hasNext_returns_false_when_at_end_of_sequence() {
    Assert::false(Sequence::$EMPTY->iterator()->hasNext());
  }

  #[Test, Expect(NoSuchElement::class)]
  public function next_throws_exception_when_at_end_of_sequence() {
    Sequence::$EMPTY->iterator()->next();
  }

  #[Test, Values('util.data.unittest.Enumerables::validArrays')]
  public function iterator($input) {
    Assert::equals([1, 2, 3], $this->iterated(Sequence::of($input)->iterator()));
  }

  #[Test, Values('util.data.unittest.Enumerables::fixedArrays')]
  public function may_iterate_sequence_based_on_a_fixed_enumerable_more_than_once($input) {
    $seq= Sequence::of($input);
    $this->iterated($seq->iterator());
    $this->iterated($seq->iterator());
  }

  #[Test, Values('util.data.unittest.Enumerables::streamedArrays')]
  public function cannot_iterate_sequence_based_on_a_streamed_enumerable_more_than_once($input) {
    $seq= Sequence::of($input);
    $this->iterated($seq->iterator());

    Assert::throws(CannotReset::class, function() use($seq) {
      $this->iterated($seq->iterator());
    });
  }

  #[Test, Values('util.data.unittest.Enumerables::validArrays')]
  public function sequence_of_iterator($input) {
    $this->assertSequence([1, 2, 3], Sequence::of(Sequence::of($input)->iterator()));
  }
}