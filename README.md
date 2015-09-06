# Foomo.Monolog

_A Foomo module wrapping Monolog for PSR-3 compatible logging._

## Monolog

[Monolog](https://github.com/Seldaek/monolog) sends your logs to files, sockets, inboxes, databases and various
web services. It uses [log handlers](https://github.com/Seldaek/monolog/blob/master/doc/02-handlers-formatters-processors.md#handlers) to do so. Special handlers allow you to build advanced logging strategies. Each log channel can have it's own handlers.

The library implements the [PSR-3](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md) interface that you can type-hint against in your own libraries to keep a maximum of interoperability.

## Configuration

Handlers and processors can be configured with a Foomo config named: `Foomo.Monolog.channel`<br>
Add the config to your module by using a subdomain like `monolog-<CHANNEL_NAME>`<br>

If your module does not offer a config for a specific channel, Foomo.Monolog will fallback to Foomo.Monolog's default config.

## Integration

Your/Project/Logger.php

```php
<?php

namespace Your\Project;

use Foomo\Monolog\AbstractLogger;

class Logger extends AbstractLogger {

	const CHANNEL_APP = 'app';
	const CHANNEL_PERFORMANCE = 'performance';
	const CHANNEL_SERVICE = 'service';

	/**
	 * @param string $channelName
	 * @param string $moduleName
	 *
	 * @return Logger
	 */
	public static function getLogger($channelName = self::CHANNEL_APP, $moduleName = Module::NAME)
	{
		return parent::getLogger($channelName, $moduleName);
	}

}
```

If your module offers some static helper methods in Your/Project.php, than you should add a helper function to get a logger there:

```php
	/**
	 * @param string $channelName
	 *
	 * @return \Your\Project\Logger
	 */
	public static function getLogger($channelName = \Your\Project\Logger::NAME)
	{
		return \Your\Project\Logger::getLogger($channelName, Module::NAME);
	}
```

## Usage

By default you can just use the Foomo.Monolog Logger:

```php
# debug logging
\Foomo\Monolog\Logger::getLogger('channel_name', 'Your.Project')->debug('some log record message', [$context]);
```

If your applications implements a helper method to get a logger, your code should look like this:

```php
# debug logging
\Your\Project::getLogger(\Your\Project\Logger::CHANNEL_APP)->debug('some log record message', [$context]);
```


## More resources (about logging):

* [Foomo.org](http://www.foomo.org/)
* [Application Logging: Best Practices – I don't always test my code. But when I do, it's in production.](http://de.slideshare.net/biggiedata/application-logging-conf-2012-clint-s1-sept-2012-copy)
* [Application Logging: What, When, How](http://java.dzone.com/news/application-logging-what-when)
* [The Art Of Application Logging](http://de.slideshare.net/benwaine/phpnw2012-logging)
* [Logging best practices](http://dev.splunk.com/view/logging-best-practices/SP-CAAADP6)
* [Logging, Processing and Monitoring Data using Talend, ElasticSearch, Logstash and Kibana](http://java.dzone.com/articles/realtime-event-logging-complex)
* [Logging with Monolog in Symfony2](http://www.webfactory.de/blog/logging-with-monolog-in-symfony2)
* [How to use Logstash with Monolog](https://coderwall.com/p/irhi_q)
* [Tracking errors with Logstash and Sentry](http://clarkdave.net/2014/01/tracking-errors-with-logstash-and-sentry/)
* [Turbo Charge your Logs](http://de.slideshare.net/jeremycook0/turbo-charge-your-logs)
* [LogStash - Yes, logging can be awesome](http://de.slideshare.net/jamtur01/yes-logging-can-be-awesome)
* [Measuring and Logging Everything in Real-Time](http://sssslide.com/speakerdeck.com/bastianhofmann/measuring-and-logging-everything-in-real-time-1)
