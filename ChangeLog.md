Data sequences change log
=========================

## 1.0.0 / 2014-11-03

* Added support to omit value function in `Collectors::toMap()`. The collector
  then used the element itself as a value - @thekid
* Added experimental support to install via Composer - @thekid
* Merged PR #2: Make `skip()` and `limit()` also accept closures
  (@thekid)
* Added supoort for `util.Filter` class in XP Framework. See xp-framework/rfc#98
  and xp-framework/rfc#266
  (@thekid)
* Added support for PHP 5.5 generators (though minimum version requirement is
  still PHP 5.4) - @thekid