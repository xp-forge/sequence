<?php namespace util\data;

use util\NoSuchElementException;

/**
 * An optional
 *
 * @test  xp://util.data.unittest.OptionalTest
 */
class Optional extends \lang\Object implements \IteratorAggregate {
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
  protected function __construct($value, $present) {
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
    return new self($value, true);
  }

  /** @return php.Iterator */
  public function getIterator() {
    return new \ArrayIterator($this->present ? [$this->value] : []);
  }

  /** @return bool */
  public function present() {
    return $this->present;
  }

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
   * Gets this optional's value, or a given default value, if no value is present.
   *
   * @param  var $default
   * @return var
   */
  public function orElse($default) {
    return $this->present ? $this->value : $default;
  }
}