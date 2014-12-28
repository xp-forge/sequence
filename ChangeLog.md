Data sequences change log
=========================

## ?.?.? / ????-??-??

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