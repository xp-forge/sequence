<?php namespace util\data;

use util\NoSuchElementException;
use util\Filter;
use util\Objects;

/**
 * An optional
 *
 * @test  xp://util.data.unittest.OptionalTest
 */
class Optional implements \lang\Value, \IteratorAggregate {
  public static $EMPTY;

  protected $value;
  protected $present;

  static function __static() {
    self::$EMPTY= new self(null, false);
  }

  /**
   * Create a given Optional instance
   *
   * @param  var $value
   * @param  bool $present
   */
  public function __construct($value, $present= true) {
    $this->value= $value;
    $this->present= $present;
  }

  /**
   * Creates a new Optional
   *
   * @param  var $value
   * @param  self
   */
  public static function of($value) {
    return null === $value ? self::$EMPTY : new self($value, true);
  }

  /** @return php.Iterator */
  public function getIterator() {
    return new \ArrayIterator($this->present ? [$this->value] : []);
  }

  /** @return bool */
  public function present() { return $this->present; }

  /**
   * Gets this optional's value
   *
   * @return var
   * @throws util.NoSuchElementException if no value is present.
   */
  public function get() {
    if ($this->present) return $this->value;

    throw new NoSuchElementException('Optional value not present');
  }

  /**
   * Gets this optional's value, or a given default value if no value is present.
   *
   * @param  var $default
   * @return var
   */
  public function orElse($default) {
    return $this->present ? $this->value : $default;
  }

  /**
   * Gets this optional's value, or invoke a given supplier if no value is present.
   *
   * @param  function(): var $supplier
   * @return var
   */
  public function orUse($supplier) {
    return $this->present ? $this->value : Functions::$SUPPLY->newInstance($supplier)->__invoke();
  }

  /**
   * Gets this optional or a new optional with the given value if no value is present
   *
   * @param  var $default
   * @return self
   */
  public function whenAbsent($default) {
    if ($this->present) {
      return $this;
    } else if ($default instanceof self) {
      return $default;
    } else if ($default instanceof \Closure) {
      return $this->whenAbsent($default());
    } else {
      return self::of($default);
    }
  }

  /**
   * Returns a new optional by applying the given mapper to this optional's value.
   * If this optional is empty, returns itself.
   *
   * @param  var $predicate either a util.Filter instance or a function
   * @return self
   * @throws lang.IllegalArgumentException
   */
  public function filter($predicate) {
    if (!$this->present) return self::$EMPTY;

    if ($predicate instanceof Filter || is('util.Filter<?>', $predicate)) {
      $filter= Functions::$APPLY->cast([$predicate, 'accept']);
    } else {
      $filter= Functions::$APPLY->newInstance($predicate);
    }

    return $filter($this->value) ? $this : self::$EMPTY;
  }

  /**
   * Returns a new optional by applying the given mapper to this optional's value.
   * If this optional is empty, returns itself.
   *
   * @param  function(var): var $function
   * @return self
   * @throws lang.IllegalArgumentException
   */
  public function map($function) {
    if (!$this->present) return self::$EMPTY;

    return self::of(Functions::$APPLY->newInstance($function)->__invoke($this->value));
  }

  /**
   * Returns a hashcode
   *
   * @return strintg
   */
  public function hashCode() {
    return 'O'.($this->present ? Objects::hashOf($this->value) : 'N');
  }

  /**
   * Compares this optional to another given value
   *
   * @param  var $value
   * @return int
   */
  public function compareTo($value) {
    return $value instanceof self
      ? Objects::compare([$this->present, $this->value], [$value->present, $value->value])
      : 1
    ;
  }

  /**
   * Creates a string representation of this optional
   *
   * @return string
   */
  public function toString() {
    if ($this->present) {
      return nameof($this).'@'.Objects::stringOf($this->value);
    } else {
      return nameof($this).'<EMPTY>';
    }
  }
}