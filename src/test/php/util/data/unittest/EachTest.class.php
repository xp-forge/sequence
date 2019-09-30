<?php namespace util\data\unittest;

use io\streams\MemoryOutputStream;
use lang\IllegalArgumentException;
use unittest\actions\VerifyThat;
use util\cmd\Console;
use util\data\Sequence;

class EachTest extends AbstractSequenceTest {

  /** @return var[][] */
  protected function invalidArguments() {
    return array_filter($this->noncallables(), function($args) { return null !== $args[0]; });
  }

  #[@test]
  public function each() {
    $collect= [];
    Sequence::of([1, 2, 3, 4])->each(function($e) use(&$collect) {
      $collect[]= $e;
    });
    $this->assertEquals([1, 2, 3, 4], $collect);
  }

  #[@test]
  public function with_key() {
    $collect= [];
    Sequence::of([1, 2, 3, 4])->each(function($e, $key) use(&$collect) {
      $collect[]= $key;
    });
    $this->assertEquals([0, 1, 2, 3], $collect);
  }

  #[@test, @values([[[1, 2, 3, 4]], [[]]])]
  public function returns_number_of_processed_elements_with_func($input) {
    $this->assertEquals(sizeof($input), Sequence::of($input)->each(function($e) { }));
  }

  #[@test, @values([[[1, 2, 3, 4]], [[]]])]
  public function returns_number_of_processed_elements_with_null($input) {
    $this->assertEquals(sizeof($input), Sequence::of($input)->each());
  }

  #[@test]
  public function writing_to_stream() {
    $out= new MemoryOutputStream();

    Sequence::of([1, 2, 3, 4])->each([$out, 'write']);

    $this->assertEquals('1234', $out->getBytes());
  }

  #[@test]
  public function writing_to_console_out() {
    $orig= Console::$out->getStream();
    $out= new MemoryOutputStream();

    try {
      Console::$out->setStream($out);
      Sequence::of([1, 2, 3, 4])->each('util.cmd.Console::write', []);
    } finally {
      Console::$out->setStream($orig);
    }

    $this->assertEquals('1234', $out->getBytes());
  }

  #[@test]
  public function with_var_export() {
    ob_start();

    Sequence::of([1, 2, 3, 4])->each('var_export', $args= [false]);

    $bytes= ob_get_contents();
    ob_end_clean();
    $this->assertEquals('1234', $bytes);
  }

  #[@test, @values('invalidArguments'), @expect(IllegalArgumentException::class)]
  public function raises_exception_when_given($noncallable) {
    Sequence::of([])->each($noncallable);
  }

  #[@test, @expect(IllegalArgumentException::class)]
  public function raises_exception_when_given_null_and_args() {
    Sequence::of([])->each(null, []);
  }

  #[@test, @action([new VerifyThat(function() {
  #  return PHP_VERSION_ID >= 70100 && !defined('HHVM_VERSION_ID');
  #})])]
  public function each_with_void() {
    $this->assertEquals(4, Sequence::of([1, 2, 3, 4])->each(eval('return function(int $e): void { };')));
  }
}