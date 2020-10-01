<?php namespace util\data\unittest;

use unittest\{Assert, Test};
use util\data\Sequence;

class SequenceResultSetTest extends AbstractSequenceTest {
  const PAGE = 3;

  protected $results= [
    ['id' => 0, 'name' => 'Timm'],
    ['id' => 1, 'name' => 'Alex'],
    ['id' => 2, 'name' => 'Dude'],
    ['id' => 3, 'name' => 'Test']
  ];

  protected $fetched;

  /**
   * Loads result sets in a paged manner
   *
   * @param  int $offset
   * @param  int $limit
   * @return var[]
   */
  protected function loadPage($offset, $limit) {
    $this->fetched[]= $offset;
    return array_slice($this->results, $offset, $limit);
  }

  /**
   * Lazy fetching sequence
   *
   * @param  int $limit
   * @param  int $page
   * @return util.data.Sequence
   */
  protected function records($limit= self::PAGE, $page= 0) {
    $records= $this->loadPage($page * $limit, $limit);
    return Sequence::of($records, sizeof($records) < $limit ? null : function() use($page, $limit) {
      return $this->records($limit, ++$page);
    });
  }

  #[Test]
  public function three_records() {
    $this->fetched= [];
    $values= $this->records(3)->map(function($e) { return $e['name']; })->toArray();
    Assert::equals(
      ['fetched' => [0, 3], 'values' => ['Timm', 'Alex', 'Dude', 'Test']],
      ['fetched' => $this->fetched, 'values' => $values]
    );
  }

  #[Test]
  public function two_records() {
    $this->fetched= [];
    $values= $this->records(2)->map(function($e) { return $e['name']; })->toArray();
    Assert::equals(
      ['fetched' => [0, 2, 4], 'values' => ['Timm', 'Alex', 'Dude', 'Test']],
      ['fetched' => $this->fetched, 'values' => $values]
    );
  }
}