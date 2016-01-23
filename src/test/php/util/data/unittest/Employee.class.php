<?php namespace util\data\unittest;

class Employee extends \lang\Object {
  protected $id;
  protected $name;
  protected $department;
  protected $years;

  /**
   * Creates a new employee
   *
   * @param  int $id
   * @param  string $name
   * @param  string $department
   * @param  int $years
   */
  public function __construct($id, $name, $department, $years) {
    $this->id= $id;
    $this->name= $name;
    $this->department= $department;
    $this->years= $years;
  }

  /** @return int */
  public function id() { return $this->id; }

  /** @return string */
  public function name() { return $this->name; }

  /** @return string */
  public function department() { return $this->department; }

  /** @return int */
  public function years() { return $this->years; }

  /** @return bool */
  public function isDinosaur() { return $this->years > 10; }

  /**
   * Creates a string representation
   *
   * @return string
   */
  public function toString() {
    return nameof($this).'('.
      'id= '.$this->id.', name= '.$this->name.', department= '.$this->department.', years= '.$this->years.
    ')';
  }

  /**
   * Compares this employee to another given value
   *
   * @param  var $cmp
   * @return bool
   */
  public function equals($cmp) {
    return $cmp instanceof self && $this->id === $cmp->id;
  }
}