<?php namespace util\data;

use peer\Socket;
use lang\Runtime;
use lang\IllegalStateException;
use io\IOException;

/**
 * 
 */
class LocalWorkerProcess extends \lang\Object {

  public function __construct($class, $args= []) {
    $this->cmd= $class.' ['.implode(', ', $args).']';
    $this->proc= Runtime::getInstance()->newInstance(null, 'class', $class, $args);
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

  public function pass($element) {
    $this->comm->write(serialize($element)."\n");
  }

  public function result() {
    return unserialize($this->comm->readLine());
  }

  public function handle() {
    return $this->comm->getHandle();
  }

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
