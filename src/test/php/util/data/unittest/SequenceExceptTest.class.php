<?php namespace util\data\unittest;

use test\{Assert, Test};
use util\data\Sequence;

class SequenceExceptTest extends AbstractSequenceTest {

  #[Test]
  public function except_empty() {
    $this->assertSequence([1, 2, 3, 4], Sequence::of([1, 2, 3, 4])->except());
  }

  #[Test]
  public function except_one() {
    $this->assertSequence([1, 2, 4], Sequence::of([1, 2, 3, 4])->except(3));
  }

  #[Test]
  public function except_multiple() {
    $this->assertSequence([1, 3], Sequence::of([1, 2, 3, 4])->except(2, 4));
  }

  #[Test]
  public function except_varargs() {
    $this->assertSequence([1, 3], Sequence::of([1, 2, 3, 4])->except(...[2, 4]));
  }

  #[Test]
  public function except_uses_object_comparison() {
    $a= new Person(1, 'A');
    $b= new Person(2, 'B');
    $c= new Person(3, 'C');

    $this->assertSequence([$a, $c], Sequence::of([$a, $b, $c])->except(new Person(2, 'B')));
  }

  #[Test]
  public function except_uses_array_comparison() {
    $a= ['id' => 1, 'name' => 'A'];
    $b= ['id' => 2, 'name' => 'B'];
    $c= ['id' => 3, 'name' => 'C'];

    $this->assertSequence([$a, $c], Sequence::of([$a, $b, $c])->except(['id' => 2, 'name' => 'B']));
  }
}