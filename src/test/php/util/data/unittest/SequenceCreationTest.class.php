<?php namespace util\data\unittest;

use util\data\Sequence;
use lang\IllegalArgumentException;

/**
 * Tests the three Sequence class' creation methods `of()`, `iterate()`
 * and `generate()`.
 *
 * @see  xp://util.data.Sequence
 */
class SequenceCreationTest extends AbstractSequenceTest {

  #[@test]
  public function of_with_one_argument() {
    $this->assertSequence([1, 2, 3], Sequence::of([1, 2, 3]));
  }

  #[@test]
  public function of_with_multiple_arguments() {
    $this->assertSequence([1, 2, 3, 4, 5, 6, 7, 8, 9], Sequence::of([1, 2], [3, 4, 5], [6, 7, 8, 9]));
  }

  #[@test, @values('util.data.unittest.Enumerables::valid')]
  public function can_create_via_of($input, $name) {
    $this->assertInstanceOf(Sequence::class, Sequence::of($input), $name);
  }

  #[@test, @expect(IllegalArgumentException::class), @values('util.data.unittest.Enumerables::invalid')]
  public function invalid_type_for_of($input) {
    Sequence::of($input);
  }

  #[@test, @values('unaryops')]
  public function can_create_via_iterate($input, $name) {
    $this->assertInstanceOf(Sequence::class, Sequence::iterate(0, $input), $name);
  }

  #[@test, @expect(IllegalArgumentException::class), @values('noncallables')]
  public function invalid_type_for_iterate($input) {
    Sequence::iterate(0, $input);
  }

  #[@test, @values('suppliers')]
  public function can_create_via_generate($input) {
    $this->assertInstanceOf(Sequence::class, Sequence::generate($input));
  }

  #[@test, @expect(IllegalArgumentException::class), @values('noncallables')]
  public function invalid_type_for_generate($input) {
    Sequence::generate($input);
  }

  #[@test]
  public function passing_null_to_of_yields_an_empty_sequence() {
    $this->assertEquals(Sequence::$EMPTY, Sequence::of(null));
  }
}