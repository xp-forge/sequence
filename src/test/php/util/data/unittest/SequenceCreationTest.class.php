<?php namespace util\data\unittest;

use util\data\Sequence;

/**
 * Tests the three Sequence class' creation methods `of()`, `iterate()`
 * and `generate()`.
 *
 * @see  xp://util.data.Sequence
 */
class SequenceCreationTest extends AbstractSequenceTest {

  #[@test, @values('util.data.unittest.Enumerables::valid')]
  public function can_create_via_of($input, $name) {
    $this->assertInstanceOf(Sequence::class, Sequence::of($input), $name);
  }

  #[@test, @expect('lang.IllegalArgumentException'), @values('util.data.unittest.Enumerables::invalid')]
  public function invalid_type_for_of($input) {
    Sequence::of($input);
  }

  #[@test, @values('unaryops')]
  public function can_create_via_iterate($input, $name) {
    $this->assertInstanceOf(Sequence::class, Sequence::iterate(0, $input), $name);
  }

  #[@test, @expect('lang.IllegalArgumentException'), @values('noncallables')]
  public function invalid_type_for_iterate($input) {
    Sequence::iterate(0, $input);
  }

  #[@test, @values('suppliers')]
  public function can_create_via_generate($input) {
    $this->assertInstanceOf(Sequence::class, Sequence::generate($input));
  }

  #[@test, @expect('lang.IllegalArgumentException'), @values('noncallables')]
  public function invalid_type_for_generate($input) {
    Sequence::generate($input);
  }

  #[@test]
  public function passing_null_to_of_yields_an_empty_sequence() {
    $this->assertEquals(Sequence::$EMPTY, Sequence::of(null));
  }
}