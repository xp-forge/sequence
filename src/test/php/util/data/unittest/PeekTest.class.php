<?php namespace util\data\unittest;

use util\data\Sequence;
use io\streams\MemoryOutputStream;
use util\cmd\Console;
use lang\IllegalArgumentException;
use unittest\actions\VerifyThat;

class PeekTest extends AbstractSequenceTest {

  #[@test, @expect(IllegalArgumentException::class), @values(['@non-existant-func@'])]
  public function invalid($arg) {
    Sequence::empty()->peek($arg);
  }

  #[@test]
  public function values() {
    $debug= [];
    Sequence::of([1, 2, 3, 4])
      ->filter(function($e) { return $e % 2 > 0; })
      ->peek(function($e) use(&$debug) { $debug[]= $e; })
      ->each()
    ;
    $this->assertEquals([1, 3], $debug);
  }

  #[@test]
  public function keys() {
    $debug= [];
    Sequence::of([1, 2, 3, 4])
      ->filter(function($e) { return $e % 2 > 0; })
      ->peek(function($e, $key) use(&$debug) { $debug[]= $key; })
      ->each()
    ;
    $this->assertEquals([0, 2], $debug);
  }

  #[@test]
  public function writing_to_console_out() {
    $orig= Console::$out->getStream();
    $out= new MemoryOutputStream();
    Console::$out->setStream($out);

    try {
      Console::$out->setStream($out);
      Sequence::of([1, 2, 3, 4])->peek('util.cmd.Console::write', [])->each();
    } finally {
      Console::$out->setStream($orig);
    }

    Console::$out->setStream($orig);    
    $this->assertEquals('1234', $out->getBytes());
  }

  #[@test]
  public function with_var_export() {
    ob_start();

    Sequence::of([1, 2, 3, 4])->peek('var_export', $args= [false])->each();

    $bytes= ob_get_contents();
    ob_end_clean();
    $this->assertEquals('1234', $bytes);
  }

  #[@test, @values('noncallables'), @expect(IllegalArgumentException::class)]
  public function raises_exception_when_given($noncallable) {
    Sequence::of([])->peek($noncallable);
  }

  #[@test, @action([new VerifyThat(function() {
  #  return PHP_VERSION_ID >= 70100 && !defined('HHVM_VERSION_ID');
  #})])]
  public function each_with_void() {
    $this->assertEquals(4, Sequence::of([1, 2, 3, 4])->peek(eval('return function(int $e): void { };'))->each());
  }
}