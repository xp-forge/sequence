<?php namespace util\data\unittest;

use lang\XPClass;
use unittest\{Assert, Before, Test, Values};
use util\collections\{HashSet, HashTable, Vector};
use util\data\{Collector, Collectors, Sequence};

class CollectorsTest {
  private $people;

  /**
   * Sets up test, initializing people member
   */
  #[Before]
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
    Assert::instance(HashTable::class, $actual);
    $compare= [];
    foreach ($actual as $pair) {
      $compare[$pair->key]= $pair->value;
    }
    return Assert::equals($expected, $compare);
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

  /** @return var[][] */
  private function dinosaurEmployees() {
    return [
      [function($e) { return $e->years() > 10; }],
      [[Employee::class, 'isDinosaur']]
    ];
  }

  #[Test, Values('employeesName')]
  public function toList($nameOf) {
    Assert::equals(['Timm', 'Alex', 'Dude'], Sequence::of($this->people)
      ->map($nameOf)
      ->collect(Collectors::toList())
      ->elements()
    );
  }

  #[Test, Values('employeesName')]
  public function toList_with_extraction($nameOf) {
    Assert::equals(['Timm', 'Alex', 'Dude'], Sequence::of($this->people)
      ->collect(Collectors::toList($nameOf))
      ->elements()
    );
  }

  #[Test, Values('employeesName')]
  public function toSet($nameOf) {
    Assert::equals(['Timm', 'Alex', 'Dude'], Sequence::of($this->people)
      ->map($nameOf)
      ->collect(Collectors::toSet())
      ->toArray()
    );
  }

  #[Test, Values('employeesName')]
  public function toSet_with_extraction($nameOf) {
    Assert::equals(['Timm', 'Alex', 'Dude'], Sequence::of($this->people)
      ->collect(Collectors::toSet($nameOf))
      ->toArray()
    );
  }

  #[Test, Values('employeesName')]
  public function toCollection_with_HashSet_class($nameOf) {
    Assert::equals(['Timm', 'Alex', 'Dude'], Sequence::of($this->people)
      ->map($nameOf)
      ->collect(Collectors::toCollection(XPClass::forName('util.collections.HashSet')))
      ->toArray()
    );
  }

  #[Test, Values('employeesName')]
  public function toMap($nameOf) {
    $map= new HashTable();
    $map[1549]= 'Timm';
    $map[1552]= 'Alex';
    $map[6100]= 'Dude';

    Assert::equals($map, Sequence::of($this->people)->collect(Collectors::toMap(
      function($e) { return $e->id(); },
      $nameOf
    )));
  }

  #[Test, Values('employeesName')]
  public function toMap_uses_complete_value_if_value_function_omitted($nameOf) {
    $map= new HashTable();
    $map['Timm']= $this->people[1549];
    $map['Alex']= $this->people[1552];
    $map['Dude']= $this->people[6100];

    Assert::equals($map, Sequence::of($this->people)->collect(Collectors::toMap(
      $nameOf,
      null
    )));
  }

  #[Test]
  public function toMap_can_use_sequence_keys() {
    $map= new HashTable();
    $map['color']= 'green';

    Assert::equals($map, Sequence::of(['color' => 'green'])->collect(Collectors::toMap()));
  }

  #[Test]
  public function toMap_key_function_can_be_omitted() {
    $map= new HashTable();
    $map['color']= 'GREEN';

    Assert::equals($map, Sequence::of(['color' => 'green'])->collect(Collectors::toMap(
      null,
      'strtoupper'
    )));
  }

  #[Test]
  public function collect_with_key() {
    $result= Sequence::of(['color' => 'green', 'price' => 12.99])->collect(new Collector(
      function() { return []; },
      function(&$result, $arg, $key) { $result[strtoupper($key)]= $arg; }
    ));
    Assert::equals(['COLOR' => 'green', 'PRICE' => 12.99], $result);
  }

  #[Test, Values('employeesYears')]
  public function summing_years($yearsOf) {
    Assert::equals(33, Sequence::of($this->people)
      ->collect(Collectors::summing($yearsOf))
    );
  }

  #[Test, Values('employeesYears')]
  public function summing_elements($yearsOf) {
    Assert::equals(33, Sequence::of($this->people)
      ->map($yearsOf)
      ->collect(Collectors::summing())
    );
  }

  #[Test, Values('employeesYears')]
  public function averaging_years($yearsOf) {
    Assert::equals(11, Sequence::of($this->people)
      ->collect(Collectors::averaging($yearsOf))
    );
  }

  #[Test, Values('employeesYears')]
  public function averaging_elements($yearsOf) {
    Assert::equals(11, Sequence::of($this->people)
      ->map($yearsOf)
      ->collect(Collectors::averaging())
    );
  }

  #[Test]
  public function counting() {
    Assert::equals(3, Sequence::of($this->people)
      ->collect(Collectors::counting())
    );
  }

  #[Test, Values('employeesDepartment')]
  public function mapping_by_department($departmentOf) {
    Assert::equals(new Vector(['B', 'I', 'I']), Sequence::of($this->people)
      ->collect(Collectors::mapping($departmentOf))
    );
  }

  #[Test]
  public function joining_names() {
    Assert::equals('Timm, Alex, Dude', Sequence::of($this->people)
      ->map(function($e) { return $e->name(); })
      ->collect(Collectors::joining())
    );
  }

  #[Test]
  public function joining_names_with_semicolon() {
    Assert::equals('Timm;Alex;Dude', Sequence::of($this->people)
      ->map(function($e) { return $e->name(); })
      ->collect(Collectors::joining(';'))
    );
  }

  #[Test]
  public function joining_names_with_prefix_and_suffix() {
    Assert::equals('(Timm, Alex, Dude)', Sequence::of($this->people)
      ->map(function($e) { return $e->name(); })
      ->collect(Collectors::joining(', ', '(', ')'))
    );
  }

  #[Test, Values('employeesDepartment')]
  public function groupingBy($departmentOf) {
    $this->assertHashTable(
      ['B' => new Vector([$this->people[1549]]), 'I' => new Vector([$this->people[1552], $this->people[6100]])],
      Sequence::of($this->people)->collect(Collectors::groupingBy($departmentOf))
    );
  }

  #[Test]
  public function groupingBy_with_summing_of_years() {
    $this->assertHashTable(['B' => 15, 'I' => 18], Sequence::of($this->people)
      ->collect(Collectors::groupingBy(
        function($e) { return $e->department(); },
        Collectors::summing(function($e) { return $e->years(); })
      ))
    );
  }

  #[Test]
  public function groupingBy_with_averaging_of_years() {
    $this->assertHashTable(['B' => 15, 'I' => 9], Sequence::of($this->people)
      ->collect(Collectors::groupingBy(
        function($e) { return $e->department(); },
        Collectors::averaging(function($e) { return $e->years(); })
      ))
    );
  }

  #[Test, Values('dinosaurEmployees')]
  public function partitioningBy($moreThanTen) {
    $this->assertHashTable(
      [true => new Vector([$this->people[1549], $this->people[1552]]), false => new Vector([$this->people[6100]])],
      Sequence::of($this->people)->collect(Collectors::partitioningBy($moreThanTen))
    );
  }

  #[Test]
  public function partitioningBy_handles_non_booleans() {
    $this->assertHashTable(
      [true => new Vector(['Test', 'Unittest']), false => new Vector(['Trial & Error'])],
      Sequence::of(['Test', 'Unittest', 'Trial & Error'])->collect(Collectors::partitioningBy(function($e) {
        return stristr($e, 'Test');
      }))
    );
  }

  #[Test]
  public function collecting_with_key() {
    Sequence::of($this->people)
      ->collecting($result, new Collector(
        function() { return []; },
        function(&$return, $element, $key) { $return[$element->department()][]= $key; }
      ))
      ->each()
    ;
    Assert::equals(['B' => [1549], 'I' => [1552, 6100]], $result);
  }
}