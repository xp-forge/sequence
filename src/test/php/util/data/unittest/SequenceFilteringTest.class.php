<?php namespace util\data\unittest;

use util\data\Sequence;

class SequenceFilteringTest extends AbstractSequenceTest {

  #[@test]
  public function with_function() {
    $this->assertSequence([2, 4], Sequence::of([1, 2, 3, 4])->filter(function($e) { return 0 === $e % 2; }));
  }

  #[@test]
  public function with_is_string_native_function() {
    $this->assertSequence(['Hello', 'World'], Sequence::of(['Hello', 1337, 'World'])->filter('is_string'));
  }

  #[@test]
  public function with_filter_instance() {
    $this->assertSequence(['Hello', 'World'], Sequence::of(['Hello', '', 'World'])->filter(newinstance('util.Filter', [], [
      'accept' => function($e) { return strlen($e) > 0; }
    ])));
  }

  #[@test]
  public function with_generic_filter_instance() {
    $this->assertSequence(['Hello', 'World'], Sequence::of(['Hello', '', 'World'])->filter(newinstance('util.Filter<string>', [], [
      'accept' => function($e) { return strlen($e) > 0; }
    ])));
  }

  #[@test, @values('noncallables'), @expect('lang.IllegalArgumentException')]
  public function raises_exception_when_given($noncallable) {
    Sequence::of([])->filter($noncallable);
  }
}