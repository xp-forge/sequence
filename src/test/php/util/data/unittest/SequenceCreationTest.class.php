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
    $this->assertInstanceOf('util.data.Sequence', Sequence::of($input), $name);
  }

  #[@test, @expect('lang.IllegalArgumentException'), @values('util.data.unittest.Enumerables::invalid')]
  public function invalid_type_for_of($input) {
    Sequence::of($input);
  }

  #[@test]
  public function can_create_via_iterate() {
    $this->assertInstanceOf('util.data.Sequence', Sequence::iterate(0, function($i) { return ++$i; }));
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function cannot_create_via_iterate_when_arg_is_missing() {
    $this->assertInstanceOf('util.data.Sequence', Sequence::iterate(0, function() { /* Empty */ }));
  }

  #[@test, @expect('lang.IllegalArgumentException'), @values('noncallables')]
  public function invalid_type_for_iterate($input) {
    Sequence::iterate(0, $input);
  }

  #[@test]
  public function can_create_via_generate() {
    $this->assertInstanceOf('util.data.Sequence', Sequence::generate(function() { return rand(0, 1000); }));
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function cannot_create_via_iterate_when_excess_arg_present() {
    $this->assertInstanceOf('util.data.Sequence', Sequence::generate(function($i) { /* Empty */ }));
  }

  #[@test, @expect('lang.IllegalArgumentException'), @values('noncallables')]
  public function invalid_type_for_generate($input) {
    Sequence::generate($input);
  }
}