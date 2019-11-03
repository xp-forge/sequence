<?php namespace util\data\unittest;

use lang\IllegalArgumentException;
use unittest\Assert;
use util\data\Sequence;

/**
 * Tests the three Sequence class' creation methods `of()`, `iterate()`
 * and `generate()`.
 *
 * @see  xp://util.data.Sequence
 */
class SequenceCreationTest extends AbstractSequenceTest {

  #[@test, @expect(IllegalArgumentException::class)]
  public function missing_argument() {
    Sequence::of();
  }

  #[@test, @values('util.data.unittest.Enumerables::valid')]
  public function can_create_via_of($input, $name) {
    Assert::instance(Sequence::class, Sequence::of($input), $name);
  }

  #[@test, @expect(IllegalArgumentException::class), @values('util.data.unittest.Enumerables::invalid')]
  public function invalid_type_for_of($input) {
    Sequence::of($input);
  }

  #[@test, @values('unaryops')]
  public function can_create_via_iterate($input, $name) {
    Assert::instance(Sequence::class, Sequence::iterate(0, $input), $name);
  }

  #[@test, @expect(IllegalArgumentException::class), @values('noncallables')]
  public function invalid_type_for_iterate($input) {
    Sequence::iterate(0, $input);
  }

  #[@test, @values('suppliers')]
  public function can_create_via_generate($input) {
    Assert::instance(Sequence::class, Sequence::generate($input));
  }

  #[@test, @expect(IllegalArgumentException::class), @values('noncallables')]
  public function invalid_type_for_generate($input) {
    Sequence::generate($input);
  }

  #[@test]
  public function passing_null_to_of_yields_an_empty_sequence() {
    Assert::equals(Sequence::$EMPTY, Sequence::of(null));
  }

  #[@test]
  public function passing_sequence_to_of_yields_itself() {
    $sequence= Sequence::of([1, 2, 3]);
    $this->assertSequence([1, 2, 3], Sequence::of($sequence));
  }


  #[@test, @values([
  #  [[1, 2, 3, 4, 5, 6]],
  #  [[1, 2, 3], [4, 5, 6]],
  #  [[1], [2, 3], [4, 5, 6]],
  #  [[1, 2], null, [3, 4, 5, 6]]
  #])]
  public function multiple_arguments_supported_in_of(... $input) {
    $this->assertSequence([1, 2, 3, 4, 5, 6], Sequence::of(...$input));
  }
}