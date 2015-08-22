Logging
=======

[![Build Status on TravisCI](https://secure.travis-ci.org/xp-framework/logging.svg)](http://travis-ci.org/xp-framework/logging)
[![XP Framework Module](https://raw.githubusercontent.com/xp-framework/web/master/static/xp-framework-badge.png)](https://github.com/xp-framework/core)
[![BSD Licence](https://raw.githubusercontent.com/xp-framework/web/master/static/licence-bsd.png)](https://github.com/xp-framework/core/blob/master/LICENCE.md)
[![Required PHP 5.4+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-5_4plus.png)](http://php.net/)
[![Supports PHP 7.0+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-7_0plus.png)](http://php.net/)
[![Supports HHVM 3.4+](https://raw.githubusercontent.com/xp-framework/web/master/static/hhvm-3_4plus.png)](http://hhvm.com/)
[![Latest Stable Version](https://poser.pugx.org/xp-framework/logging/version.png)](https://packagist.org/packages/xp-framework/logging)

Logging for the XP Framework.

Example
-------

```php
use util\log\LogCategory;
use util\log\ConsoleAppender;
use util\ServiceNotAvailableException;
use lang\Throwable;

$logger= (new LogCategory())->withAppender(new ConsoleAppender());
$logger->info('Starting application');

try {
  $service->operation();
} catch (ServiceNotAvailableException $e) {
  $logger->warn('Service not available', $e);
} catch (Throwable $t) {
  $logger->error('Error during service invocation', $t);
}
```

Appenders
---------
The following appenders are available:

* `util.log.FileAppender` - Logs to a local file
* `util.log.ConsoleAppender` - Logs to console
* `util.log.ColoredConsoleAppender` - Logs to console using colors depending on log level
* `util.log.SmtpAppender` - Logs by email to a given email address
* `util.log.StreamAppender` - Logs to any output stream from `io.streams`.
* `util.log.SyslogAppender` - Logs using syslog facility
* `util.log.BufferedAppender` - Logs to a memory buffer

Layout
------
The default log layout includes time, pid, level and message implemented by the `util.log.layout.DefaultLayout` class. It renders as follows:

```
[13:43:39  4368  info] Starting application
```

The log layout can be changed by instantiating the `util.log.layout.PatternLayout`, passing a format string and using the appenders `setLayout()` method to use it. The format string consists of format tokens preceded by a percent sign (%) and any other character. The following format tokens are 
supported:

* `%m` - Message
* `%c` - Category name
* `%l` - Log level - lowercase
* `%L` - Log level - uppercase
* `%t` - Time in HH:MM:SS
* `%p` - Process ID
* `%%` - A literal percent sign (%)
* `%n` - A line break
* `%x` - Context information, if available
