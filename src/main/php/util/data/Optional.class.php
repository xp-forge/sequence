<?php namespace util\data;

use util\NoSuchElementException;

/**
 * An optional
 *
 * @test  xp://util.data.unittest.OptionalTest
 */
#[@generic(self= 'T')]
class Optional extends \lang\Object {
  public static $EMPTY;

  protected $value;
  protected $present;

  static function __static() {
    self::$EMPTY= new self(null, false);
  }

  /**
   * Create a given Optional instance
   *
   * @param  T $value
   * @param  bool $present
   */
  protected function __construct($value, $present) {
    $this->value= $value;
    $this->present= $present;
  }

  /**
   * Creates a new Optional
   *
   * @param  T $value
   * @param  self<T>
   */
  #[@generic(params= 'T', return= 'self<T>')]
  public static function of($value) {
    return new self($value, true);
  }

  /** @return bool */
  public function present() {
    return $this->present;
  }

  /**
   * Gets this optional's value
   *
   * @return T
   * @throws util.NoSuchElementException if no value is present.
   */
  #[@generic(return= 'T')]
  public function get() {
    if ($this->present) return $this->value;

    throw new NoSuchElementException('Optional value not present');
  }

  /**
   * Gets this optional's value, or a given default value, if no value is present.
   *
   * @param  T $default
   * @return T
   */
  #[@generic(params= 'T', return= 'T')]
  public function orElse($default) {
    return $this->present ? $this->value : $default;
  }
}