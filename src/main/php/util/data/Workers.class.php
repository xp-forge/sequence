<?php namespace util\data;

/**
 * 
 */
interface Workers {

  public function enqueue($element);

  public function pending();

  public function dequeue();
}