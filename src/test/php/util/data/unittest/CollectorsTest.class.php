<?php namespace util\data\unittest;

use util\data\Sequence;
use util\data\Collectors;
use util\collections\Vector;
use util\collections\HashSet;
use util\collections\HashTable;
use lang\XPClass;

class CollectorsTest extends \unittest\TestCase {
  protected $people;

  /**
   * Sets up test, initializing people member
   */
  public function setUp() {
    $this->people= [
      1549 => new Employee(1549, 'Timm', 'B', 15),
      1552 => new Employee(1552, 'Alex', 'I', 14),
      6100 => new Employee(6100, 'Dude', 'I', 4)
    ];
  }

  /**
   * Compares a hashtable against an expected map.
   *
   * @param  [:var] $expected
   * @param  util.collections.HashTable $actual
   * @throws unittest.AssertionFailedError
   */
  protected function assertHashTable($expected, $actual) {
    $this->assertInstanceOf('util.collections.HashTable', $actual);
    $compare= [];
    foreach ($actual as $pair) {
      $compare[$pair->key]= $pair->value;
    }
    return $this->assertEquals($expected, $compare);
  }

  #[@test]
  public function toList() {
    $this->assertEquals(new Vector(['Timm', 'Alex', 'Dude']), Sequence::of($this->people)
      ->map(function($e) { return $e->name(); })
      ->collect(Collectors::toList())
    );
  }

  #[@test]
  public function toSet() {
    $set= new HashSet();
    $set->addAll(['Timm', 'Alex', 'Dude']);

    $this->assertEquals($set, Sequence::of($this->people)
      ->map(function($e) { return $e->name(); })
      ->collect(Collectors::toSet())
    );
  }

  #[@test]
  public function toCollection_with_HashSet_class() {
    $set= new HashSet();
    $set->addAll(['Timm', 'Alex', 'Dude']);

    $this->assertEquals($set, Sequence::of($this->people)
      ->map(function($e) { return $e->name(); })
      ->collect(Collectors::toCollection(XPClass::forName('util.collections.HashSet')))
    );
  }

  #[@test]
  public function toMap() {
    $map= new HashTable();
    $map[1549]= 'Timm';
    $map[1552]= 'Alex';
    $map[6100]= 'Dude';

    $this->assertEquals($map, Sequence::of($this->people)->collect(Collectors::toMap(
      function($e) { return $e->id(); },
      function($e) { return $e->name(); }
    )));
  }

  #[@test]
  public function toMap_uses_complete_value_if_value_function_omitted() {
    $map= new HashTable();
    $map['Timm']= $this->people[1549];
    $map['Alex']= $this->people[1552];
    $map['Dude']= $this->people[6100];

    $this->assertEquals($map, Sequence::of($this->people)->collect(Collectors::toMap(
      function($e) { return $e->name(); }
    )));
  }

  #[@test]
  public function summing_years() {
    $this->assertEquals(33, Sequence::of($this->people)
      ->collect(Collectors::summing(function($e) { return $e->years(); }))
    );
  }

  #[@test]
  public function summing_elements() {
    $this->assertEquals(33, Sequence::of($this->people)
      ->map(function($e) { return $e->years(); })
      ->collect(Collectors::summing())
    );
  }

  #[@test]
  public function averaging_years() {
    $this->assertEquals(11, Sequence::of($this->people)
      ->collect(Collectors::averaging(function($e) { return $e->years(); }))
    );
  }

  #[@test]
  public function averaging_elements() {
    $this->assertEquals(11, Sequence::of($this->people)
      ->map(function($e) { return $e->years(); })
      ->collect(Collectors::averaging())
    );
  }

  #[@test]
  public function counting() {
    $this->assertEquals(3, Sequence::of($this->people)
      ->collect(Collectors::counting())
    );
  }

  #[@test]
  public function mapping_by_department() {
    $this->assertEquals(new Vector(['B', 'I', 'I']), Sequence::of($this->people)
      ->collect(Collectors::mapping(function($e) { return $e->department(); }))
    );
  }

  #[@test]
  public function joining_names() {
    $this->assertEquals('Timm, Alex, Dude', Sequence::of($this->people)
      ->map(function($e) { return $e->name(); })
      ->collect(Collectors::joining())
    );
  }

  #[@test]
  public function joining_names_with_semicolon() {
    $this->assertEquals('Timm;Alex;Dude', Sequence::of($this->people)
      ->map(function($e) { return $e->name(); })
      ->collect(Collectors::joining(';'))
    );
  }

  #[@test]
  public function joining_names_with_prefix_and_suffix() {
    $this->assertEquals('(Timm, Alex, Dude)', Sequence::of($this->people)
      ->map(function($e) { return $e->name(); })
      ->collect(Collectors::joining(', ', '(', ')'))
    );
  }

  #[@test]
  public function groupingBy() {
    $this->assertHashTable(
      ['B' => new Vector([$this->people[1549]]), 'I' => new Vector([$this->people[1552], $this->people[6100]])],
      Sequence::of($this->people)->collect(Collectors::groupingBy(function($e) { return $e->department(); }))
    );
  }

  #[@test]
  public function groupingBy_with_summing_of_years() {
    $this->assertHashTable(['B' => 15, 'I' => 18], Sequence::of($this->people)
      ->collect(Collectors::groupingBy(
        function($e) { return $e->department(); },
        Collectors::summing(function($e) { return $e->years(); })
      ))
    );
  }

  #[@test]
  public function groupingBy_with_averaging_of_years() {
    $this->assertHashTable(['B' => 15, 'I' => 9], Sequence::of($this->people)
      ->collect(Collectors::groupingBy(
        function($e) { return $e->department(); },
        Collectors::averaging(function($e) { return $e->years(); })
      ))
    );
  }

  #[@test]
  public function partitioningBy() {
    $this->assertHashTable(
      [true => new Vector([$this->people[1549], $this->people[1552]]), false => new Vector([$this->people[6100]])],
      Sequence::of($this->people)->collect(Collectors::partitioningBy(function($e) { return $e->years() > 10; }))
    );
  }
}