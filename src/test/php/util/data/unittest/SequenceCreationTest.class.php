<?php namespace util\data\unittest;

use util\data\Sequence;

/**
 * Tests the three Sequence class' creation methods `of()`, `iterate()`
 * and `generate()`.
 *
 * @see  xp://util.data.Sequence
 */
class SequenceCreationTest extends AbstractSequenceTest {

  #[@test, @values('valid')]
  public function can_create_via_of($input, $name) {
    $this->assertInstanceOf('util.data.Sequence', Sequence::of($input), $name);
  }

  #[@test, @expect('lang.IllegalArgumentException'), @values('invalid')]
  public function invalid_type_for_of($input) {
    Sequence::of($input);
  }

  #[@test, @values('callables')]
  public function can_create_via_iterate($input, $name) {
    $this->assertInstanceOf('util.data.Sequence', Sequence::iterate(0, $input), $name);
  }

  #[@test, @expect('lang.IllegalArgumentException'), @values('noncallables')]
  public function invalid_type_for_iterate($input) {
    Sequence::iterate(0, $input);
  }

  #[@test, @values('callables')]
  public function can_create_via_generate($input) {
    $this->assertInstanceOf('util.data.Sequence', Sequence::generate($input));
  }

  #[@test, @expect('lang.IllegalArgumentException'), @values('noncallables')]
  public function invalid_type_for_generate($input) {
    Sequence::generate($input);
  }
}