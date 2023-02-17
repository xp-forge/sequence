<?php namespace util\data\unittest;

use lang\IllegalArgumentException;
use test\{Assert, Expect, Test, Values};
use util\data\Sequence;

/**
 * Tests the three Sequence class' creation methods `of()`, `iterate()`
 * and `generate()`.
 *
 * @see  xp://util.data.Sequence
 */
class SequenceCreationTest extends AbstractSequenceTest {
  use Enumerables;

  #[Test, Expect(IllegalArgumentException::class)]
  public function missing_argument() {
    Sequence::of();
  }

  #[Test, Values(from: 'valid')]
  public function can_create_via_of($input, $name) {
    Assert::instance(Sequence::class, Sequence::of($input), $name);
  }

  #[Test, Expect(IllegalArgumentException::class), Values(from: 'invalid')]
  public function invalid_type_for_of($input) {
    Sequence::of($input);
  }

  #[Test, Values(from: 'unaryops')]
  public function can_create_via_iterate($input, $name) {
    Assert::instance(Sequence::class, Sequence::iterate(0, $input), $name);
  }

  #[Test, Expect(IllegalArgumentException::class), Values(from: 'noncallables')]
  public function invalid_type_for_iterate($input) {
    Sequence::iterate(0, $input);
  }

  #[Test, Expect(IllegalArgumentException::class)]
  public function null_for_iterate() {
    Sequence::iterate(0, null);
  }

  #[Test, Values(from: 'suppliers')]
  public function can_create_via_generate($input) {
    Assert::instance(Sequence::class, Sequence::generate($input));
  }

  #[Test, Expect(IllegalArgumentException::class), Values(from: 'noncallables')]
  public function invalid_type_for_generate($input) {
    Sequence::generate($input);
  }

  #[Test, Expect(IllegalArgumentException::class)]
  public function null_for_generate() {
    Sequence::generate(null);
  }

  #[Test]
  public function passing_null_to_of_yields_an_empty_sequence() {
    Assert::equals(Sequence::$EMPTY, Sequence::of(null));
  }

  #[Test]
  public function passing_sequence_to_of_yields_itself() {
    $sequence= Sequence::of([1, 2, 3]);
    $this->assertSequence([1, 2, 3], Sequence::of($sequence));
  }

  #[Test, Values([[[1, 2, 3, 4, 5, 6]], [[1, 2, 3], [4, 5, 6]], [[1], [2, 3], [4, 5, 6]], [[1, 2], null, [3, 4, 5, 6]]])]
  public function multiple_arguments_supported_in_of(... $input) {
    $this->assertSequence([1, 2, 3, 4, 5, 6], Sequence::of(...$input));
  }
}