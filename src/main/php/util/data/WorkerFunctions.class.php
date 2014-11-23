<?php namespace util\data;

/**
 * 
 */
class WorkerFunctions extends \lang\Object implements Workers {
  protected $functions, $offset;

  /**
   * Creates a new WorkerFunctions instance
   *
   * @param  php.Closure[] $functions
   */
  public function __construct(array $functions) {
    $this->functions= $functions;
    $this->offset= 0;
  }

  public function enqueue($element) {
    $this->result= $this->functions[$this->offset]->__invoke($element);
    if (++$this->offset >= sizeof($this->functions)) {
      $this->offset= 0;
    }
  }

  public function dequeue() {
    return $this->result;
  }
}