<?php namespace util\data;

use peer\Socket;
use lang\Runtime;
use lang\IllegalStateException;
use io\IOException;
use util\collections\Queue;

/**
 * A worker process that runs on this machine.
 *
 * @test  xyp://util.data.unittest.LocalWorkerProcessTest
 */
class LocalWorkerProcess extends \lang\Object {
  protected $queue, $pending, $cmd, $proc, $comm;

  /**
   * Creates a new locally running worker process
   *
   * @param  string $class
   * @param  string[] $args
   */
  public function __construct($class, $args= []) {
    $this->queue= new Queue();
    $this->pending= false;
    $this->cmd= $class.' ['.implode(', ', $args).']';
    $this->proc= Runtime::getInstance()->newInstance(null, 'class', $class, $args);
    $this->connect();
  }

  /**
   * Initiates communication with worker
   *
   * @return void
   * @throws lang.IllegalStateException
   */
  protected function connect() {
    $line= $this->proc->out->readLine();
    if ('+' === $line{0}) {
      sscanf($line, '+ %s:%d', $host, $port);
      $this->comm= new Socket($host, $port);
      $this->comm->connect();
    } else {
      $this->proc->close();
      throw new IllegalStateException('Cannot initiate communication with worker: '.$line);
    }
  }

  public function handle() { return $this->comm->getHandle(); }

  public function pending() { return $this->pending; }

  /**
   * Pass in an element for processing
   *
   * @param  var $element
   */
  public function pass($element) {
    if ($this->pending) {
      $this->queue->put($element);
    } else {
      $this->comm->write(serialize($element)."\n");
      $this->pending= true;
    }
  }

  /**
   * Returns a processing result
   *
   * @return var
   * @throws lang.IllegalStateException
   */
  public function result() {
    if (!$this->pending) {
      if ($this->queue->isEmpty()) {
        throw new IllegalStateException('No pending results');
      }

      $this->comm->write(serialize($this->queue->get())."\n");
    }

    $element= unserialize($this->comm->readLine());
    $this->pending= false;
    return $element;
  }

  /**
   * Shuts down this worker process
   *
   * @return int
   */
  public function shutdown() {
    if (-1 === $this->proc->exitValue()) {
      try {
        $this->comm->write("SHUTDOWN\n");
        $this->comm->close();
      } catch (IOException $ignored) { }

      $this->proc->close();
    }
    return $this->proc->exitValue();
  }

  public function __destruct() {
    $this->shutdown();
  }

  public function toString() {
    return $this->getClassName().'(pid= '.$this->proc->getProcessId().', cmd= '.$this->cmd.')';
  }
}
