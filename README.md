Data sequences
==============

[![XP Framework Mdodule](https://raw.githubusercontent.com/xp-framework/web/master/static/xp-framework-badge.png)](https://github.com/xp-framework/core)
[![BSD Licence](https://raw.githubusercontent.com/xp-framework/web/master/static/licence-bsd.png)](https://github.com/xp-framework/core/blob/master/LICENCE.md)
[![Required PHP 5.4+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-5_4plus.png)](http://php.net/)

This API allows working with data sequences of different kinds in a functional style, e.g. map/reduce.

Examples
--------

```php
use util\data\Sequence;

$return= Sequence::of([1, 2, 3, 4])
  ->filter(function($e) { return 0 === $e % 2; })
  ->toArray()
;
// [2, 4]

$return= Sequence::of([1, 2, 3, 4])
  ->map(function($e) { return $e * 2; })
  ->toArray()
;
// [2, 4, 6, 8]

$names= Sequence::of($this->people)
  ->map(function($e) { return $e->name(); })
  ->collect(Collectors::joining(', '))
;
// "Timm, Alex, Dude"

$experience= Sequence::of($this->employees)
  ->collect(Collectors::groupingBy(
    function($e) { return $e->department(); },
    Collectors::averaging(function($e) { return $e->years(); })
  ))
;
// HashTable[2] {
//  Department("A") => 12.8
//  Department("B") => 3.5
// }
```

Further reading
---------------

* The [java.util.stream package](http://docs.oracle.com/javase/8/docs/api/java/util/stream/package-summary.html) 
* [JDK8: Stream style](http://de.slideshare.net/SergeyKuksenko/jdk8-stream-style) - by Sergey Kuksenko, Performance Engineering at Oracle on Dec 03, 2013 
* [Processing Data with Java SE 8 Streams, Part 1](http://www.oracle.com/technetwork/articles/java/ma14-java-se-8-streams-2177646.html)