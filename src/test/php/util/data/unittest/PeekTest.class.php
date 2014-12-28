<?php namespace util\data\unittest;

use util\data\Sequence;
use io\streams\MemoryOutputStream;
use util\cmd\Console;

class PeekTest extends AbstractSequenceTest {

  #[@test, @expect('lang.IllegalArgumentException'), @values(['@non-existant-func@'])]
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

    Sequence::of([1, 2, 3, 4])->peek('util.cmd.Console::write')->each();

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

  #[@test, @values('noncallables'), @expect('lang.IllegalArgumentException')]
  public function raises_exception_when_given($noncallable) {
    Sequence::of([])->peek($noncallable);
  }
}