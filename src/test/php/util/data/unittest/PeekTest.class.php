<?php namespace util\data\unittest;

use io\streams\MemoryOutputStream;
use lang\IllegalArgumentException;
use unittest\Assert;
use unittest\actions\RuntimeVersion;
use util\cmd\Console;
use util\data\Sequence;

class PeekTest extends AbstractSequenceTest {

  #[@test, @expect(IllegalArgumentException::class), @values(['@non-existant-func@'])]
  public function invalid($arg) {
    Sequence::$EMPTY->peek($arg);
  }

  #[@test]
  public function values() {
    $debug= [];
    Sequence::of([1, 2, 3, 4])
      ->filter(function($e) { return $e % 2 > 0; })
      ->peek(function($e) use(&$debug) { $debug[]= $e; })
      ->each()
    ;
    Assert::equals([1, 3], $debug);
  }

  #[@test]
  public function keys() {
    $debug= [];
    Sequence::of([1, 2, 3, 4])
      ->filter(function($e) { return $e % 2 > 0; })
      ->peek(function($e, $key) use(&$debug) { $debug[]= $key; })
      ->each()
    ;
    Assert::equals([0, 2], $debug);
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
    Assert::equals('1234', $out->getBytes());
  }

  #[@test]
  public function with_var_export() {
    ob_start();

    Sequence::of([1, 2, 3, 4])->peek('var_export', $args= [false])->each();

    $bytes= ob_get_contents();
    ob_end_clean();
    Assert::equals('1234', $bytes);
  }

  #[@test, @values('noncallables'), @expect(IllegalArgumentException::class)]
  public function raises_exception_when_given($noncallable) {
    Sequence::of([])->peek($noncallable);
  }

  #[@test, @action([new RuntimeVersion('>=7.1.0')])]
  public function each_with_void() {
    Assert::equals(4, Sequence::of([1, 2, 3, 4])->peek(eval('return function(int $e): void { };'))->each());
  }
}