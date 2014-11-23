<?php namespace util\data;

/**
 * 
 */
interface Workers {

  public function enqueue($element);

  public function dequeue();
}