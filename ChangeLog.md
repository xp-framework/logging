s Logging change log
==================

## ?.?.? / ????-??-??

## 8.2.0 / 2018-08-11

* Made Logger::configure() return the logger itself - @thekid
* Merged PR #7: Add Syslog UDP appender - @johannes85, @thekid

## 8.1.1 / 2018-08-10

* Fix issue #8: Hashcode should be unique - @thekid

## 8.1.0 / 2018-01-20

* Added ability to use functions inside `debug()`, `info()`, `warn()` and
  `error()` which will be invoked at the moment of formatting
  (@thekid)
* Fixed inconsistency between PHP 5 and 7 with map formatting - @thekid
* Fixed test suite not entirely running on XP9 - @thekid

## 8.0.0 / 2017-05-29

* Merged PR #6: Logging DSL - @thekid
* **Heads up:** Deprecated singleton `util.log.Logger`  - @thekid
* Rewrote all named `LogCategory` methods to use native varargs - @thekid
* **Heads up:** Dropped PHP 5.5 support - @thekid
* Merged PR #5: XP9 Compat - @thekid

## 7.1.0 / 2016-08-28

* Added forward compatibility with XP 8.0.0: Refrain from using deprecated
  `util.Properties::fromString()`
  (@thekid)

## 7.0.0 / 2016-02-21

* **Adopted semantic versioning. See xp-framework/rfc#300** - @thekid 
* Added version compatibility with XP 7 - @thekid

## 6.6.0 / 2016-01-10

* **Heads up: Upgrade your runners before using this release!**
  It uses class path precedence as defined in xp-runners/reference#11
  (@thekid)

## 6.5.1 / 2015-12-20

* Declared dependency on xp-framework/collections and xp-framework/unittest,
  which have since been extracted from XP core.
  (@thekid)

## 6.5.0 / 2015-09-27

* **Heads up: Bumped minimum PHP version required to PHP 5.5**. See PR #4
  (@thekid)

## 6.4.3 / 2015-08-22

* Fixed util.log.LogObserver not accepting LogCategory instances - @thekid
* Implemented PR #1: Patternlayout: %d for dates - @thekid
* Code QA: Adopt to newest unittest coding standares  - @thekid

## 6.4.2 / 2015-08-22

* **Heads up: Split library from xp-framework/core as per xp-framework/rfc#301**
  (@thekid)
