<?php namespace util\data;

use io\IOException;

/**
 * 
 */
class WorkerProcesses extends \lang\Object implements Workers {
  protected $processes, $timeout, $offset, $waitHandles;

  /**
   * Creates a new WorkerProcesses instance
   *
   * @param  util.data.WorkerProcess[] $processes
   * @param  double $timeout
   */
  public function __construct(array $processes, $timeout= null) {
    $this->processes= $processes;
    $this->timeout= $timeout;
    $this->offset= 0;
    $this->pending= [];
    $this->waitHandles= [];
    foreach ($this->processes as $i => $process) {
      $this->waitHandles[$i]= $process->handle();
    }
  }

  public function enqueue($element) {
    $this->processes[$this->offset]->pass($element);
    $this->pending[$this->offset]= true;

    if (++$this->offset >= sizeof($this->processes)) {
      $this->offset= 0;
      return false;
    }
    return true;
  }

  public function pending() {
    return !empty($this->pending);
  }

  /**
   * Wait for the first worker process to become ready, and return its result
   *
   * @param  var[] $r
   * @param  double $timeout
   * @param  util.data.WorkerProcess
   */
  protected function waitFor($r, $timeout) {
    if (null === $timeout) {
      $tv_sec= $tv_usec= null;
    } else {
      $tv_sec= intval(floor($timeout));
      $tv_usec= intval(($timeout - floor($timeout)) * 1000000);
    }

    $w= $e= null;
    if (false === stream_select($r, $w, $e, $tv_sec, $tv_usec) || empty($r)) {
      throw new IOException('No results present'.($this->timeout ? ' after '.$this->timeout.' seconds' : ''));
    }

    $offset= key($r);
    unset($this->pending[$offset]);
    return $this->processes[$offset];
  }

  public function dequeue() {
    return $this->waitFor($this->waitHandles, $this->timeout)->result();
  }
}