<?php namespace util\data;

use lang\IllegalArgumentException;

/**
 * Pivot
 * =====
 *
 * ```
 * .-----------------------------------------------------------------------------.
 * | Category  | 2015-05-10 | 2015-05-11 | Sum      | Percentage | Count | Avg.   |
 * |-----------|------------|------------|----------|------------|-------|--------|
 * | OK        | n:100      | n:95       | n:195    | n:97.5     | n:2   | n:97.5 |
 * | GOOD      | n:2        | n:0        | n:2      | n:1.0      | n:1   | n:2    |
 * | ERROR     | n:0        | n:3        | n:3      | n:1.5      | n:3   | n:1.0  |
 * | ^- client | ^- n:0     | ^- n:2     | ^- n:2   | ^- n:1.0   | n:3   | n:0.67 |
 * |   ^- 403  |   ^- n:0   |   ^- n:1   |   ^- n:1 |   ^- n:0.5 | n:1   | n:1.0  |
 * |   ^- 404  |   ^- n:0   |   ^- n:1   |   ^- n:1 |   ^- n:0.5 | n:2   | n:0.5  |
 * | ^- server | ^- n:0     | ^- n:1     | ^- n:1   | ^- n:0.5   | n:1   | n:0.5  |
 * |   ^- 500  |   ^- n:0   |   ^- n:1   |   ^- n:1 |   ^- n:0.5 | n:1   | n:0.5  |
 * |-----------|------------|------------|----------|------------|-------|--------|
 * | Total     | n:102      | n:98       | n:200    |            | n:14  |        |
 * `-----------------------------------------------------------------------------´
 * ```
 *
 * Accessing by category
 * ---------------------
 * Use the `sum()`, `average()` and `percentage()` methods to access the values.
 * In the above example, `sum("OK")['n']` = 195, `average("ERROR")['n']` = 1.5
 * and `percentage("GOOD")['n']` = 1.0. The subtotals e.g. for client errors can
 * be accessed by passing in varargs: `sum("ERROR", "client")['n']` = 2. The number
 * of records included in the fact is returned by `count("OK")` = 2.
 *
 * To iterate over the categories, use the `rows()` method.
 * Accessing a single row can be done via `row()`.
 *
 * Accessing by date
 * -----------------
 * Pass the date to the `total()` method, e.g. `total("2015-05-10")['n']` = 102.
 *
 * To iterate over the dates, use the `columns()` method.
 * Accessing a single column can be done via `column()`.
 *
 * Grand totals
 * ------------
 * Use the `total()` method without any argument to access the grand total for all
 * values (`['n' => 200]`).
 *
 * The `count()` method will return many records we processed on (14).
 *
 * @test  xp://util.data.unittest.PivotTest
 */
class Pivot extends \lang\Object {
  const COUNT = 0;
  const TOTAL = 1;
  const ROWS  = 2;
  const COLS  = 3;

  private $groupBy, $spreadOn, $aggregate;
  private $facts= null;

  /**
   * Creates a new pivot table
   * 
   * @param  (function(var): var)[] $groupBy
   * @param  function(var): var $spreadOn
   * @param  [:(function(var): var)] $aggregate
   * @throws lang.IllegalArgumentException
   */
  public function __construct(array $groupBy, \Closure $spreadOn= null, array $aggregate) {
    if (empty($groupBy)) {
      throw new IllegalArgumentException('Group by cannot be empty');
    }
    $this->groupBy= array_merge($groupBy, [null]);
    $this->spreadOn= $spreadOn;
    $this->aggregate= $aggregate;
  }

  /**
   * Adds a row to this pivot
   *
   * @param  var $row
   * @return void
   */
  public function add($row) {
    $sums= [];
    foreach ($this->aggregate as $name => $func) {
      $sums[$name]= $func($row);
    }
    $spread= $this->spreadOn ? $this->spreadOn->__invoke($row) : null;

    $ptr= &$this->facts;
    foreach ($this->groupBy as $group) {
      if (isset($ptr)) {
        foreach ($sums as $name => $sum) {
          $ptr[self::TOTAL][$name]+= $sum;
        }
        $ptr[self::COUNT]++;
      } else {
        $ptr= [self::COUNT => 1, self::TOTAL => $sums, self::ROWS => [], self::COLS => []];
      }

      if ($this->spreadOn) {
        $cols= &$ptr[self::COLS][$spread];
        if (isset($cols)) {
          foreach ($sums as $name => $sum) {
            $cols[self::TOTAL][$name]+= $sum;
          }
          $cols[self::COUNT]++;
        } else {
          $cols= [self::COUNT => 1, self::TOTAL => $sums];
        }
      }

      $group && $ptr= &$ptr[self::ROWS][$group($row)];
    }
  }

  /**
   * Selects a fact
   *
   * @param  var[] $paths
   * @return [:var] An array with ROWS, COLS, and TOTAL
   */
  private function fact($paths) {
    $ptr= &$this->facts;
    foreach ($paths as $path) {
      $ptr= &$ptr[self::ROWS][$path];
    }
    return $ptr;
  }

  /**
   * Returns all rows
   *
   * @param  string* $path
   * @return var[]
   */
  public function rows() {
    return array_keys($this->fact(func_get_args())[self::ROWS]);
  }

  /**
   * Returns a single row
   *
   * @param  string* $path
   * @return var[]
   */
  public function row($path) {
    return $this->fact(func_get_args());
  }

  /**
   * Returns count
   *
   * @param  string* $path
   * @return int
   */
  public function count() {
    return $this->fact(func_get_args())[self::COUNT];
  }

  /**
   * Returns the sum
   *
   * @param  string* $path
   * @return [:var]
   */
  public function sum($path) {
    return $this->fact(func_get_args())[self::TOTAL];
  }

  /**
   * Returns the percentage of the grant total
   *
   * @param  string* $path
   * @return [:double]
   */
  public function percentage($path) {
    $fact= $this->fact(func_get_args());
    $return= [];
    foreach ($fact[self::TOTAL] as $name => $value) {
      $return[$name]= $value / $this->facts[self::TOTAL][$name] * 100;
    }
    return $return;
  }

  /**
   * Returns the average of the values
   *
   * @param  string* $path
   * @return [:double]
   */
  public function average($path) {
    $fact= $this->fact(func_get_args());
    $return= [];
    foreach ($fact[self::TOTAL] as $name => $value) {
      $return[$name]= $value / $fact[self::COUNT];
    }
    return $return;
  }

  /**
   * Returns all columns
   *
   * @return var[]
   */
  public function columns() {
    return array_keys($this->facts[self::COLS]);
  }

  /**
   * Returns a single column
   *
   * @param  string $column
   * @return var[]
   */
  public function column($column) {
    return $this->facts[self::COLS][$column];
  }

  /**
   * Returns total
   *
   * @param  string $column
   * @return [:var]
   */
  public function total($column= null) {
    return null === $column ? $this->facts[self::TOTAL] : $this->facts[self::COLS][$column][self::TOTAL];
  }
}