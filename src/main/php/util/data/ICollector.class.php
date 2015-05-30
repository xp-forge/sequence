<?php namespace util\data;

interface ICollector {

  /** @return function(): var */
  public function supplier();

  /** @return function(var, var): var */
  public function accumulator();

  /** @return function(var): var */
  public function finisher();
}