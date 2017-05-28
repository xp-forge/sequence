<?php namespace util\data\unittest;

class Person implements \lang\Value {
  private $id, $name;

  /**
   * Creates a new person
   *
   * @param  int $id
   * @param  string $name
   */
  public function __construct($id, $name) {
    $this->id= $id;
    $this->name= $name;
  }

  /** @return int */
  public function id() { return $this->id; }

  /** @return string */
  public function name() { return $this->name; }

  /** Creates a string representation */
  public function toString() {
    return nameof($this).'(id= '.$this->id.', name= '.$this->name.')';
  }

  /** Creates a hashcode */
  public function hashCode() {
    return 'P'.$this->id;
  }

  /**
   * Compares this employee to another given value
   *
   * @param  var $valu
   * @return int
   */
  public function compareTo($value) {
    return $value instanceof self ? $this->id - $value->id : 1;
  }
}