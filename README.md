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

* `util.log.FileAppender` - Logs to a local file
* `util.log.ConsoleAppender` - Logs to console
* `util.log.ColoredConsoleAppender` - Logs to console using colors depending on log level
* `util.log.SmtpAppender` - Logs by email to a given email address
* `util.log.StreamAppender` - Logs to any output stream from `io.streams`.
* `util.log.SyslogAppender` - Logs using syslog facility
* `util.log.BufferedAppender` - Logs to a memory buffer