<?php namespace util\data\unittest;

use util\data\Sequence;
use io\streams\MemoryOutputStream;
use util\cmd\Console;

class EachTest extends AbstractSequenceTest {

  #[@test]
  public function each() {
    $collect= [];
    Sequence::of([1, 2, 3, 4])->each(function($e) use(&$collect) {
      $collect[]= $e;
    });
    $this->assertEquals([1, 2, 3, 4], $collect);
  }

  #[@test]
  public function each_with_key() {
    $collect= [];
    Sequence::of([1, 2, 3, 4])->each(function($e, $key) use(&$collect) {
      $collect[]= $key;
    });
    $this->assertEquals([0, 1, 2, 3], $collect);
  }

  #[@test, @values([[[1, 2, 3, 4]], [[]]])]
  public function each_returns_number_of_processed_elements($input) {
    $this->assertEquals(sizeof($input), Sequence::of($input)->each(function($e) { }));
  }

  #[@test]
  public function each_writing_to_stream() {
    $out= new MemoryOutputStream();

    Sequence::of([1, 2, 3, 4])->each([$out, 'write']);

    $this->assertEquals('1234', $out->getBytes());
  }

  #[@test]
  public function each_writing_to_console_out() {
    $orig= Console::$out->getStream();
    $out= new MemoryOutputStream();
    Console::$out->setStream($out);

    Sequence::of([1, 2, 3, 4])->each('util.cmd.Console::write');

    Console::$out->setStream($orig);    
    $this->assertEquals('1234', $out->getBytes());
  }

  #[@test]
  public function each_with_var_export() {
    ob_start();

    Sequence::of([1, 2, 3, 4])->each('var_export', $args= [false]);

    $bytes= ob_get_contents();
    ob_end_clean();   
    $this->assertEquals('1234', $bytes);
  }

  #[@test, @values('noncallables'), @expect('lang.IllegalArgumentException')]
  public function each_raises_exception_when_given($noncallable) {
    Sequence::of([])->each($noncallable);
  }
}