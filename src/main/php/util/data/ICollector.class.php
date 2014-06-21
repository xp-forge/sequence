<?php namespace util\data;

interface ICollector {

  public function supplier();

  public function accumulator();

  public function finisher();
}