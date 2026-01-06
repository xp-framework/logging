Logging
=======

[![Build status on GitHub](https://github.com/xp-framework/logging/workflows/Tests/badge.svg)](https://github.com/xp-framework/logging/actions)
[![XP Framework Module](https://raw.githubusercontent.com/xp-framework/web/master/static/xp-framework-badge.png)](https://github.com/xp-framework/core)
[![BSD Licence](https://raw.githubusercontent.com/xp-framework/web/master/static/licence-bsd.png)](https://github.com/xp-framework/core/blob/master/LICENCE.md)
[![Requires PHP 7.0+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-7_0plus.svg)](http://php.net/)
[![Supports PHP 8.0+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-8_0plus.svg)](http://php.net/)
[![Latest Stable Version](https://poser.pugx.org/xp-framework/logging/version.svg)](https://packagist.org/packages/xp-framework/logging)

Logging for the XP Framework.

Example
-------

```php
use util\log\Logging;
use lang\Throwable;
use peer\ConnectException;

$logger= Logging::named('service')->toConsole();
$logger->info('Starting application');

try {
  $service->operation();
} catch (ConnectException $e) {
  $logger->warn('Service not available', $e);
} catch (Throwable $t) {
  $logger->error('Error during service invocation', $t);
}
```

Levels
------
This library supports the following levels: DEBUG, INFO, WARN and ERROR. As seen above, messages can be logged using methods named after these levels. All methods have a *printf*-style variant:

* `debug(var... $args)` and `debugf(string $format, var... $args)`.
* `info(var... $args)` and `infof(string $format, var... $args)`.
* `warn(var... $args)` and `warnf(string $format, var... $args)`.
* `error(var... $args)` and `errorf(string $format, var... $args)`.

Appenders
---------
The following appenders are available:

* `util.log.FileAppender(string $filename)` - Logs to a local file
* `util.log.ConsoleAppender()` - Logs to console
* `util.log.ColoredConsoleAppender()` - Logs to console using colors depending on log level
* `util.log.SmtpAppender(string $email, string $prefix= "", bool $sync= true)` - Logs by email to a given email address
* `util.log.StreamAppender(io.streams.OutputStream $out)` - Logs to any output stream from `io.streams`.
* `util.log.SyslogAppender(string $identifier, int $facility= LOG_USER)` - Logs using syslog facility
* `util.log.SyslogUdpAppender(string $ip= '127.0.0.1', int $port= 514, string $identifier= null, int $facility= LOG_USER, string $hostname= null)` - Logs using syslog protocol over UDP
* `util.log.BufferedAppender()` - Logs to a memory buffer

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
* `%d` - Date in YYYY-MM-DD
* `%t` - Time in HH:MM:SS
* `%p` - Process ID
* `%%` - A literal percent sign (%)
* `%n` - A line break
* `%x` - Context information, if available

Configuration
-------------
Instead of using the [Logging DSL](https://github.com/xp-framework/logging/pull/6) to create your log setup programmatically, you can use the [configuration API](https://github.com/xp-framework/logging/pull/12), which works with INI files:

```ini
[default]
uses=console|syslog|files

[console]
class=util.log.ConsoleAppender
level=ALL

[files]
class=util.log.FileAppender
args="/var/log/server.log"
level=ALL

[syslog]
class=util.log.SyslogUdpAppender
args=127.0.0.1|514|server
level=WARN|ERROR
```

Further reading
---------------
* [Log Contexts](https://github.com/xp-framework/xp-framework/pull/239)
* [Support for rolling file names](https://github.com/xp-framework/xp-framework/pull/353)