Data sequences
==============

[![Build status on GitHub](https://github.com/xp-forge/sequence/workflows/Tests/badge.svg)](https://github.com/xp-forge/sequence/actions)
[![XP Framework Module](https://raw.githubusercontent.com/xp-framework/web/master/static/xp-framework-badge.png)](https://github.com/xp-framework/core)
[![BSD Licence](https://raw.githubusercontent.com/xp-framework/web/master/static/licence-bsd.png)](https://github.com/xp-framework/core/blob/master/LICENCE.md)
[![Requires PHP 7.0+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-7_0plus.svg)](http://php.net/)
[![Supports PHP 8.0+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-8_0plus.svg)](http://php.net/)
[![Latest Stable Version](https://poser.pugx.org/xp-forge/sequence/version.png)](https://packagist.org/packages/xp-forge/sequence)

This API allows working with data sequences of different kinds in a functional style, e.g. map/reduce.

Examples
--------
### Sequence
Instances of the `util.data.Sequence` class can be created from iterable input, either an in-memory structure or a stream of data, e.g. read from a network socket. The Sequence class provides intermediate (work on single elements, return a new Sequence) and terminal (consume all elements, returning a single value) operations.

```php
use util\data\{Sequence, Collectors, Aggregations};

$return= Sequence::of([1, 2, 3, 4])
  ->filter(fn($e) => 0 === $e % 2)
  ->toArray()
;
// $return= [2, 4]

$return= Sequence::of([1, 2, 3, 4])
  ->map(fn($e) => $e * 2)
  ->toArray()
;
// $return= [2, 4, 6, 8]

$i= 0;
$return= Sequence::of([1, 2, 3, 4])
  ->counting($i)
  ->reduce(0, fn($a, $b) => $a + $b)
;
// $i= 4, $return= 10

$names= Sequence::of($this->people)
  ->map('com.example.Person::name')
  ->collect(Collectors::joining(', '))
;
// $names= "Timm, Alex, Dude"

$experience= Sequence::of($this->employees)
  ->collect(Collectors::groupingBy(
    fn($e) => $e->department(),
    Aggregations::average(fn($e) => $e->years())
  ))
;
// $experience= util.collections.HashTable[2] {
//   Department("A") => 12.8
//   Department("B") => 3.5
// }
```

### Optional
Instances of the `util.data.Optional` class are thin wrappers around possible NULL values. The operations provided by Optional class help in reducing conditional code:

```php
use util\data\Optional;

$first= Optional::of($repository->find($user));
if ($first->present()) {
  $user= $first->get();    // When Repository::find() returned non-null
}

$user= $first->orElse($this->currentUser);
$user= $first->orUse(fn() => $this->currentUser());

$name= $first
  ->filter(fn($user) => $user->isActive())
  ->whenAbsent($this->currentUser)
  ->whenAbsent(fn() => $this->guestUser())
  ->map('com.example.User::name')
  ->get()
;
```

Creation operations
-------------------
Sequences can be created from a variety of sources, and by using these static methods:

* **of** - accepts PHP arrays (zero-based as well as associative), all traversable data structures including `lang.types.ArrayList` and `lang.types.ArrayMap` as well as anything from `util.collections`, `util.XPIterator` instances, PHP iterators and iterator aggregates, PHP 5.5 generators (*yield*), as well as sequences themselves. Passing NULL will yield an empty sequence.
* **iterate** - Iterates starting with a given seed, applying a unary operator on this value and passing the result to the next invocation, forever. Combine with `limit()`!
* **generate** - Iterates forever, returning whatever the given supplier function returns. Combine with `limit()`!

Intermediate operations
-----------------------
The following operations return a new `Sequence` instance on which more intermediate or terminal operations can be invoked:

* **skip** - Skips past elements in the beginning. Using `skip(4)` skips the first four elements, `skip(function($e) { return 'initial' === $e; })` will skip all elements which equal to the string *initial*. 
* **limit** - Stops iteration when limit is reached. Using `limit(10)` stops iteration once ten elements have been returned, `limit(function($e) { return 'stop' === $e; })` will stop once the first element equal to the string *stop* is encountered.
* **filter** - Filters the sequence by a given criterion. The new sequence will only contain values for which it returns true. Accepts a function or a `util.Filter` instance.
* **map** - Maps each element in the sequence by applying a function on it and returning a new sequence with the return value of that function.
* **peek** - Calls a function for each element in the sequence; especially useful for debugging, e.g. `peek('var_dump', [])`.
* **counting** - Increments the integer given as its argument for each element in the sequence.
* **collecting** - Collects elements in this sequence to a `util.data.ICollector` instance. Unlike the terminal operation below, passes the elements on.
* **flatten** - Flattens sequences inside the sequence and returns a new list containing all values from all sequences.
* **distinct** - Returns a new sequence which only consists of unique elements. Uniqueness is calculated using the `util.Objects::hashOf()` method by default (*but can be passed another function*).
* **zip** - Combines values from this sequence with a given enumerable value, optionally using a given transformation function.
* **sorted** - Returns a sorted collection. Can be invoked with a comparator function, a `util.Comparator` instance or the sort flags from PHP's sort() function (e.g. `SORT_NUMERIC | SORT_DESC`).
* **chunked** - Returns a chunked stream with chunks not exceeding the given size. The last chunk may have a smaller size.
* **windowed** - Returns a sliding window stream - a list of element ranges that you would see if you were looking at the collection through a sliding window of the given size.

Terminal operations
-------------------
The following operations return a single value by consuming all of the sequence:

* **toArray** - will return a PHP array with zero-based keys
* **toMap** - will return a PHP associative array
* **first** - will return the first element as an `util.data.Optional` instance. A value will be present if the sequence was not empty.
* **single** - like `first()`, but raises an exception if more than one element is contained in the sequence.
* **count** - will return the number of elements in the sequence
* **min** - returns the smalles element. Compares numbers by default but may be given a comparator function or a `util.Comparator` instance.
* **max** - same as `min()`, but returns the largest element instead.
* **each** - Applies a given function to each element in the sequence, and returns the number of elements. Can be invoked without a function to consume "silently".
* **reduce** - Perform a reduction on the elements in this sequence.
* **collect** - Pass all elements in this sequence to a `util.data.ICollector` instance.

Iteration
---------
To use controlled iteration on a sequence, you can use the `foreach` statement or receive a "hasNext/next"-iterator via the `iterator()` accessor. If the sequence is based on seekable data (rule of thumb: all in-memory structures will be seekable), these operations can be repeated with the same effect. Otherwise, a `util.data.CannotReset` exception will be raised (e.g., for data streamed from a socket).

Further reading
---------------

* The [java.util.stream package](http://docs.oracle.com/javase/8/docs/api/java/util/stream/package-summary.html) 
* [JDK8: Stream style](http://de.slideshare.net/SergeyKuksenko/jdk8-stream-style) - by Sergey Kuksenko, Performance Engineering at Oracle on Dec 03, 2013 
* [Processing Data with Java SE 8 Streams, Part 1](http://www.oracle.com/technetwork/articles/java/ma14-java-se-8-streams-2177646.html)
* [Lazy sequences implementation for Java 8](https://github.com/nurkiewicz/LazySeq)
