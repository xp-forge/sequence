<?php namespace util\data\unittest;

use util\data\Sequence;
use util\Date;
use util\Comparator;

class SequenceSortingTest extends AbstractSequenceTest {

  #[@test, @values([
  #  [[-1, 1, 2, 6, 8, 11, 6100], [6100, 1, -1, 2, 8, 6, 11]],
  #  [['A', 'a', 'b', 'c'], ['c', 'a', 'A', 'b']],
  #  [['a100', 'a2', 'a20', 'b1', 'b2'], ['a2', 'a100', 'b1', 'a20', 'b2']]
  #])]
  public function sorted($result, $input) {
    $this->assertSequence($result, Sequence::of($input)->sorted());
  }

  #[@test]
  public function sorted_with_flags() {
    $this->assertSequence([3, 2, 1], Sequence::of([1, 2, 3])->sorted(SORT_NUMERIC | SORT_DESC));
  }

  #[@test]
  public function sorted_by_comparator() {
    $this->assertSequence(
      [new Date('1977-12-14'), new Date('1979-12-29')],
      Sequence::of([new Date('1979-12-29'), new Date('1977-12-14')])->sorted(newinstance('util.Comparator', [], [
        'compare' => function($a, $b) { return $b->compareTo($a); }
      ]))
    );
  }

  #[@test]
  public function sorted_by_natural_order_string_comparison() {
    $this->assertSequence(
      ['rfc1.txt', 'rfc822.txt', 'rfc2086.txt'],
      Sequence::of(['rfc1.txt', 'rfc2086.txt', 'rfc822.txt'])->sorted(
        function($a, $b) { return strnatcasecmp($a, $b); }
      ),
      'http://sourcefrog.net/projects/natsort/'
    );
  }
}