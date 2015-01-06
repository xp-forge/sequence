<?php namespace util\data\unittest;

class Person extends \lang\Object {
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
}