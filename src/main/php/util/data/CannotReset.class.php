<?php namespace util\data;

use lang\XPException;

/**
 * Indicates the underlying value is streamed and cannot be reset
 *
 * @see   util.data.Iterator#rewind
 */
class CannotReset extends XPException {

}