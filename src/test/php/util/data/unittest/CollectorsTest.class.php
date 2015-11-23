<?php namespace util\data\unittest;

use util\data\Sequence;
use util\data\Collectors;
use util\data\Collector;
use util\collections\Vector;
use util\collections\HashSet;
use util\collections\HashTable;
use lang\XPClass;

class CollectorsTest extends \unittest\TestCase {
  private $people;

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
  private function assertHashTable($expected, $actual) {
    $this->assertInstanceOf(HashTable::class, $actual);
    $compare= [];
    foreach ($actual as $pair) {
      $compare[$pair->key]= $pair->value;
    }
    return $this->assertEquals($expected, $compare);
  }

  /** @return var[][] */
  private function employeesName() {
    return [
      [function($e) { return $e->name(); }],
      [[Employee::class, 'name']]
    ];
  }

  /** @return var[][] */
  private function employeesDepartment() {
    return [
      [function($e) { return $e->department(); }],
      [[Employee::class, 'department']]
    ];
  }

  /** @return var[][] */
  private function employeesYears() {
    return [
      [function($e) { return $e->years(); }],
      [[Employee::class, 'years']]
    ];
  }

  #[@test, @values('employeesName')]
  public function toList($nameOf) {
    $this->assertEquals(['Timm', 'Alex', 'Dude'], Sequence::of($this->people)
      ->map($nameOf)
      ->collect(Collectors::toList())
      ->elements()
    );
  }

  #[@test, @values('employeesName')]
  public function toList_with_extraction($nameOf) {
    $this->assertEquals(['Timm', 'Alex', 'Dude'], Sequence::of($this->people)
      ->collect(Collectors::toList($nameOf))
      ->elements()
    );
  }

  #[@test, @values('employeesName')]
  public function toSet($nameOf) {
    $this->assertEquals(['Timm', 'Alex', 'Dude'], Sequence::of($this->people)
      ->map($nameOf)
      ->collect(Collectors::toSet())
      ->toArray()
    );
  }

  #[@test, @values('employeesName')]
  public function toSet_with_extraction($nameOf) {
    $this->assertEquals(['Timm', 'Alex', 'Dude'], Sequence::of($this->people)
      ->collect(Collectors::toSet($nameOf))
      ->toArray()
    );
  }

  #[@test, @values('employeesName')]
  public function toCollection_with_HashSet_class($nameOf) {
    $this->assertEquals(['Timm', 'Alex', 'Dude'], Sequence::of($this->people)
      ->map($nameOf)
      ->collect(Collectors::toCollection(XPClass::forName('util.collections.HashSet')))
      ->toArray()
    );
  }

  #[@test, @values('employeesName')]
  public function toMap($nameOf) {
    $map= new HashTable();
    $map[1549]= 'Timm';
    $map[1552]= 'Alex';
    $map[6100]= 'Dude';

    $this->assertEquals($map, Sequence::of($this->people)->collect(Collectors::toMap(
      function($e) { return $e->id(); },
      $nameOf
    )));
  }

  #[@test, @values('employeesName')]
  public function toMap_uses_complete_value_if_value_function_omitted($nameOf) {
    $map= new HashTable();
    $map['Timm']= $this->people[1549];
    $map['Alex']= $this->people[1552];
    $map['Dude']= $this->people[6100];

    $this->assertEquals($map, Sequence::of($this->people)->collect(Collectors::toMap(
      $nameOf,
      null
    )));
  }

  #[@test]
  public function toMap_can_use_sequence_keys() {
    $map= new HashTable();
    $map['color']= 'green';

    $this->assertEquals($map, Sequence::of(['color' => 'green'])->collect(Collectors::toMap()));
  }

  #[@test]
  public function toMap_key_function_can_be_omitted() {
    $map= new HashTable();
    $map['color']= 'GREEN';

    $this->assertEquals($map, Sequence::of(['color' => 'green'])->collect(Collectors::toMap(
      null,
      'strtoupper'
    )));
  }

  #[@test]
  public function collect_with_key() {
    $result= Sequence::of(['color' => 'green', 'price' => 12.99])->collect(new Collector(
      function() { return []; },
      function(&$result, $arg, $key) { $result[strtoupper($key)]= $arg; }
    ));
    $this->assertEquals(['COLOR' => 'green', 'PRICE' => 12.99], $result);
  }

  #[@test, @values('employeesYears')]
  public function summing_years($yearsOf) {
    $this->assertEquals(33, Sequence::of($this->people)
      ->collect(Collectors::summing($yearsOf))
    );
  }

  #[@test, @values('employeesYears')]
  public function summing_elements($yearsOf) {
    $this->assertEquals(33, Sequence::of($this->people)
      ->map($yearsOf)
      ->collect(Collectors::summing())
    );
  }

  #[@test, @values('employeesYears')]
  public function averaging_years($yearsOf) {
    $this->assertEquals(11, Sequence::of($this->people)
      ->collect(Collectors::averaging($yearsOf))
    );
  }

  #[@test, @values('employeesYears')]
  public function averaging_elements($yearsOf) {
    $this->assertEquals(11, Sequence::of($this->people)
      ->map($yearsOf)
      ->collect(Collectors::averaging())
    );
  }

  #[@test]
  public function counting() {
    $this->assertEquals(3, Sequence::of($this->people)
      ->collect(Collectors::counting())
    );
  }

  #[@test, @values('employeesDepartment')]
  public function mapping_by_department($departmentOf) {
    $this->assertEquals(new Vector(['B', 'I', 'I']), Sequence::of($this->people)
      ->collect(Collectors::mapping($departmentOf))
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