Data sequences change log
=========================

## ?.?.? / ????-??-??

* Merged PR #57: Retrieve collection parts - @thekid
* Merged PR #55: Migrate test suite - @thekid

## 10.1.0 / 2022-08-28

* Introduce dedicated `util.data.NoSuchElement` exception. It's thrown
  from `Optional::get()` if no element is present instead of the generic
  `util.NoSuchElementException`. For BC reasons, it extends the latter.
  (@thekid)
* Implemented PR #54: Implement `Sequence::single()` - which is similar
  to `first()` but raises an exception if there are more than 1 element
  in the sequence.
  (@thekid)

## 10.0.0 / 2021-10-21

* Implemented xp-framework/rfc#341, dropping compatibility with XP 9
  (@thekid)

## 9.3.0 / 2021-10-15

* Merged PR #53: Implement `Sequence::zip()` intermediate operation
  (@thekid)

## 9.2.0 / 2021-09-06

* Merged PR #52: Allow NULL for Sequence methods peek(), map(), filter(),
  skip(), limit() as well as Optional methods map() and filter()
  (@thekid)

## 9.1.1 / 2021-09-06

* Fixed PHP 8.1 compatibility for `Traversable` subtypes - @thekid

## 9.1.0 / 2021-05-13

* Added compatibility with `xp-framework/collections` version 10.0.0
  (@thekid)

## 9.0.1 / 2020-09-20

* Simplified test code, making use of anonymous classes, the RuntimeVersion
  action and `Assert::throws()`, see pull request #50
  (@thekid)

## 9.0.0 / 2020-04-10

* Implemented xp-framework/rfc#334: Drop PHP 5.6. The minimum required
  PHP version is now 7.0.0!
  (@thekid)

## 8.0.4 / 2019-12-01

* Made compatible with XP 10 - @thekid

## 8.0.3 / 2019-09-29

* Added PHP 7.4 compatibility - @thekid
* Increased test coverage to 100% - @thekid

## 8.0.2 / 2018-06-08

* Fixed PHP 7.2 compatibility - @thekid

## 8.0.1 / 2017-07-11

* Fixed `each()` and `peek()` not being able to work with functions
  with `void` return type.
  (@thekid)

## 8.0.0 / 2017-05-29

* Merged PR #45: Forward compatibility with XP 9.0.0 - @thekid

## 7.0.1 / 2017-05-20

* Refactored code to use dedicated fixture methods instead of using
  `xp` class methods *typeOf()* and *gc()*, see xp-framework/rfc#323
  (@thekid)

## 7.0.0 / 2017-05-14

* Merged PR #43 ("Remove deprecated functionality"):
  . Removed `Sequence::concat()` method deprecated in favor of a multi-
    argument version of `Sequence::of()` - see 6.4.2-RELEASE
  . Removed deprecated classes from yield refactoring released in 6.4.1
  (@thekid)

## 6.6.0 / 2017-05-06

* Added support for closures returning iterables in `Enumeration::of()`
  (@thekid)

## 6.5.1 / 2017-05-06

* Improved `each()` performance when passing extra arguments - @thekid

## 6.5.0 / 2017-05-06

* Merged PR #36: Add optional mapper to toArray() and toMap() - @thekid

## 6.4.2 / 2017-05-02

* Merged PR #42: Deprecate static concat() in favor of multiple-argument
  `of()`. Also deprecates `util.data.Iterators` class.
  (@thekid)

## 6.4.1 / 2017-04-25

* Merged PR #41: Inline collecting() and counting(). Both methods are
  now four times faster.
  (@thekid)
* **Heads up**: Deprecated the following classes which were inlined:
  . AbstractMapper, Mapper, MapperWithKey
  . AbstractWindow, Window, WindowWithKey
  . AbstractFilterable, Filterable, FilterableWithKey
  . Flattener
  . Generator
  These will be removed in the next major release!
  (@thekid)
* Merged PR #40: Inline Generator class. Improves performance of the
  creation methods `generate()` and `iterate()` roughly three times.
  (@thekid)
* Merged PR #39: Inline intermediate operations using `yield`. This
  brings a performance improvement for `filter()`, `map()`, `peek()`,
  `flatten()`, `skip()` and `limit()` with factors ranging from 4.1
  up until 8.7 for the default cases; and double performance for both
  `skip()` and `limit()` with integer limits.
  (@thekid)

## 6.4.0 / 2017-03-18

* Merged PR #35: Add `util.data.CannotReset` exception class replacing
  lang.IllegalStateException for indicating a sequence can't be processed
  more than once. This makes it easier to distinguish exception causes
  and handle them separately if necessary.
  (@thekid)

## 6.3.0 / 2016-10-18

* Merged PR #34: Add ability to use yield statements inside flatten()
  (@thekid)

## 6.2.1 / 2016-10-17

* Fixed issue with `limit()` closure being invoked with *NULL* when the
  end of the sequence is reached.
  (@thekid)

## 6.2.0 / 2016-08-28

* Merged PR #33: Add optional "function" parameter to distinct() - @thekid

## 6.1.0 / 2016-08-28

* Added forward compatibility with XP 8.0.0 - @thekid

## 6.0.0 / 2016-07-24

* Merged PR #32: Drop PHP 5.5 support, use native varargs & unpacking
  (@thekid)

## 5.2.0 / 2016-06-03

* Merged PR #31: Add optional filter to first() operation - @thekid

## 5.1.0 / 2016-04-21

* Merged PR #29: Collection API - @thekid

## 5.0.0 / 2016-02-21

* Added version compatibility with XP 7 - @thekid

## 4.1.2 / 2016-01-23

* Fix code to use `nameof()` instead of the deprecated `getClassName()`
  method from lang.Generic. See xp-framework/core#120
  (@thekid)

## 4.1.1 / 2015-12-20

* Added dependencies on collections and unittest libraries which have
  since been extracted from XP core.
  (@thekid)

## 4.1.0 / 2015-11-23

* Merged PR #28: Fix Collectors class' methods to cast given functions
  (@thekid)
* Merged PR #27: Add support for optional mapper in toSet() and toList()
  (@thekid)

## 4.0.0 / 2015-10-10

* **Heads up: Dropped PHP 5.4 support**. See pull request #26. *Note: As
  the main source is not touched, unofficial PHP 5.4 support is still
  available though not tested with Travis-CI*.
  (@thekid)

## 3.1.0 / 2015-06-20

* Merged PR #25: Implement support for using keys in collect() - @thekid
* Merged PR #24: Implement support for mapping keys - @thekid

## 3.0.0 / 2015-06-01

* Renamed whenNull() to `whenAbsent()` in util.data.Optional. This is
  more consistent with the present() method. See pull request #23
  (@thekid)
* Implemented Optional::toString(), equals() and hashCode() - @thekid
* Implemented Sequence::toString(), equals() and hashCode() - @thekid

## 2.2.1 / 2015-05-31

* Fixed issue #22: tests not running on HHVM - @thekid

## 2.2.0 / 2015-05-30

* Made Optional's constructor public and allow `new Optional(null)` to
  represent an optional with a present null value
  (@thekid)
* Changed Optional::of() to return an empty optional if `null` is passed
  (@thekid)
* Added whenNull() method to util.data.Optional to allow easy chaining
  (@thekid)
* Added filter(), map() and orUse() implementations to util.data.Optional
  See pull request #21
  (@thekid)

## 2.1.1 / 2015-04-04

* Don't keep index association when sorting, this yields arrays like 
  `[1 => 'second', 0 => 'first']`. These arrays are not zero-based and
  considered maps e.g. by typeof()
  (@thekid)

## 2.1.0 / 2015-01-18

* Heads up: Removed `sum()` operation, it can easily be rewritten to
  either `$sum= $seq->reduce(0, function($a, $b) { return $a + $b; })`
  or `$sum= $seq->collect(Collectors::summing())` which are more flexible.
  (@thekid)
* Made functions to `Collectors::summing` and `Collectors::averaging`
  optional. If omitted, the elements themselves will be used for the
  numbers.
  (@thekid)
* Added support for referring to instance methods via string. Depends
  on xp-framework/core#44, see pull request #17 - @thekid
* Added `Sequence::toMap()` - see pull request #10 - @thekid
* Added `Sequence::iterator()` - see pull request #16 - @thekid
* Added the ability to pass a variable number of arguments to
  `Sequence::concat()`. At the same time, the method is changed to
  be more liberal in what it accepts. See pull request #13.
  (@thekid)
* Implemented variant of `Sequence::each()` which iterates elements
  without callback and is thus more memory-efficient than calling
  e.g. toArray() and discarding the results. See pull request #12.
  (@thekid)

## 2.0.0 / 2014-12-22

* Made `Sequence::of(null)` return an empty sequence - @thekid
* Merged PR #5: Keys - @thekid

## 1.0.0 / 2014-11-03

* Chose to make this 1.0.0 since it's been in productive use and thus
  well proven to work - @thekid
* Added support to omit value function in `Collectors::toMap()`. The
  collector then used the element itself as a value - @thekid

## 0.7.4 / 2014-09-27

* Added experimental support to install via Composer - @thekid

## 0.7.3 / 2014-09-23

* Merged PR #2: Make `skip()` and `limit()` also accept closures
  (@thekid)

## 0.7.2 / 2014-08-15

* Added support for `util.Filter` class in XP Framework. See xp-framework/rfc#98
  and xp-framework/rfc#266
  (@thekid)

## 0.7.1 / 2014-08-12

* Added support for PHP 5.5 generators (though minimum version requirement is
  still PHP 5.4) - @thekid

## 0.7.0 / 2014-07-30

* Implemented `Sequence::flatten()` which treats each element as a sequence
  itself and returns each of its elements before continuing with the iterator's
  next element.
  (@thekid)

## 0.6.0 / 2014-07-13

* Implemented `Sequence::sorted()` which supports closures, util.Comparator
  instances and sort flags as argument.
  (@thekid)

## 0.5.1 / 2014-07-13

* Added `Sequence::$EMPTY` member - @thekid

## 0.5.0 / 2014-07-13

* Implemented `Sequence::peek()` which additionally calls the given function
  for each element processed. Useful e.g. for debugging purposes.
  (@thekid)

## 0.4.0 / 2014-07-13

* Changed `first()` method to return an `Optional` instance - @thekid

## 0.3.0 / 2014-06-22

* Exchange `Closure` type hints for `callable`. All methods now also accept
  strings pointing to functions, and array referring to class and instance
  methods. Solved internally by util.data.Closure utility class.
  (@thekid)

## 0.2.0 / 2014-06-22

* Added support for `util.XPIterator` instances - @thekid

## 0.1.0 / 2014-06-21

* Hello World! First release - @thekid