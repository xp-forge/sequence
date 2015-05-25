Data sequences
==============

[![Build Status on TravisCI](https://secure.travis-ci.org/xp-forge/sequence.svg)](http://travis-ci.org/xp-forge/sequence)
[![XP Framework Module](https://raw.githubusercontent.com/xp-framework/web/master/static/xp-framework-badge.png)](https://github.com/xp-framework/core)
[![BSD Licence](https://raw.githubusercontent.com/xp-framework/web/master/static/licence-bsd.png)](https://github.com/xp-framework/core/blob/master/LICENCE.md)
[![Required PHP 5.4+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-5_4plus.png)](http://php.net/)
[![Latest Stable Version](https://poser.pugx.org/xp-forge/sequence/version.png)](https://packagist.org/packages/xp-forge/sequence)

This API allows working with data sequences of different kinds in a functional style, e.g. map/reduce.

Examples
--------

```php
use util\data\Sequence;
use util\data\Collectors;

$return= Sequence::of([1, 2, 3, 4])
  ->filter(function($e) { return 0 === $e % 2; })
  ->toArray()
;
// $return= [2, 4]

$return= Sequence::of([1, 2, 3, 4])
  ->map(function($e) { return $e * 2; })
  ->toArray()
;
// $return= [2, 4, 6, 8]

$i= 0;
$return= Sequence::of([1, 2, 3, 4])
  ->counting($i)
  ->reduce(0, function($a, $b) { return $a + $b; }))
;
// $i= 4, $return= 10

$names= Sequence::of($this->people)
  ->map('com.example.Person::name')
  ->collect(Collectors::joining(', '))
;
// $names= "Timm, Alex, Dude"

$experience= Sequence::of($this->employees)
  ->collect(Collectors::groupingBy(
    function($e) { return $e->department(); },
    Collectors::averaging(function($e) { return $e->years(); })
  ))
;
// $experience= util.collections.HashTable[2] {
//   Department("A") => 12.8
//   Department("B") => 3.5
// }
```

Creation operations
-------------------
Sequences can be created from a variety of sources, and by using these static methods:

* **of** - accepts PHP arrays (zero-based as well as associative), all traversable data structures including `lang.types.ArrayList` and `lang.types.ArrayList` as well as anything from `util.collections`, `util.XPIterator` instances, PHP iterators and iterator aggregates, PHP 5.5 generators (*yield*), as well as sequences themselves. Passing NULL will yield an empty sequence.
* **iterate** - Iterates starting with a given seed, applying a unary operator on this value and passing the result to the next invocation, forever. Combine with `limit()`!
* **generate** - Iterates forever, returning whatever the given supplier function returns. Combine with `limit()`!
* **concat** - Concatenates a variable number of arguments with anything `of()` accepts into one large sequence.

Intermediate operations
-----------------------
The following operations return a new `Sequence` instance on which more intermediate or terminal operations can be invoked:

* **skip** - Skips past elements in the beginning. Using `skip(4)` skips the first four elements, `skip(function($e) { return 'initial' === $e; })` will skip all elements which equal to the string *initial*. 
* **limit** - Stops iteration when limit is reached. Using `limit(10)` stops iteration once ten elements have been returned, `limit(function($e) { return 'stop' === $e; })` will stop once the first element equal to the string *stop* is encountered.
* **filter** - Filters the sequence by a given criterion. The new sequence will only contain values for which it returns true. Accepts a function or a `util.Filter` instance.
* **map** - Maps each element in the sequence by applying a function on it and returning a new sequence with the return value of that function.
* **peek** - Calls a function for each element in the sequence; especially useful for debugging, e.g. `peek('var_dump', [])`.
* **counting** - Increments the integer given as its argument for each element in the sequence.
* **flatten** - Flattens sequences inside the sequence and returns a new list containing all values from all sequences.
* **disinct** - Returns a new sequence which only consists of unique elements. Uniqueness is calculated using the `util.Objects::hashOf()` method.
* **sorted** - Returns a sorted collection. Can be invoked with a comparator function, a `util.Comparator` instance or the sort flags from PHP's sort() function (e.g. `SORT_NUMERIC | SORT_DESC`).

Terminal operations
-------------------
The following operations return a single value by consuming all of the sequence:

* **toArray** - will return a PHP array with zero-based keys
* **toMap** - will return a PHP associative array
* **first** - will return the first element as an `util.data.Optional` instance. A value will be present if the sequence was not empty.
* **count** - will return the number of elements in the sequence
* **min** - returns the smalles element. Compares numbers by default but may be given a comparator function or a `util.Comparator` instance.
* **max** - same as `min()`, but returns the largest element instead.
* **each** - Applies a given function to each element in the sequence, and returns the number of elements. Can be invoked without a function to consume "silently".
* **reduce** - Perform a reduction on the elements in this sequence.
* **collect** - Pass all elements in this sequence to a `util.data.ICollector` instance.

Iteration
---------
To use controlled iteration on a sequence, you can use the `foreach` statement or receive a "hasNext/next"-iterator via the `iterator()` accessor. If the sequence is based on seekable data (rule of thumb: all in-memory structures will be seekable), these operations can be repeated with the same effect. Otherwise, a `lang.IllegalStateException` will be raised (e.g., for data streamed from a socket).

Further reading
---------------

* The [java.util.stream package](http://docs.oracle.com/javase/8/docs/api/java/util/stream/package-summary.html) 
* [JDK8: Stream style](http://de.slideshare.net/SergeyKuksenko/jdk8-stream-style) - by Sergey Kuksenko, Performance Engineering at Oracle on Dec 03, 2013 
* [Processing Data with Java SE 8 Streams, Part 1](http://www.oracle.com/technetwork/articles/java/ma14-java-se-8-streams-2177646.html)
* [Lazy sequences implementation for Java 8](https://github.com/nurkiewicz/LazySeq)
