New Relic integration for Nette Framework
=========================================

Repository [https://github.com/blueweb/newrelic-nette](https://github.com/blueweb/newrelic-nette).

## Requirements

Library `blueweb/newrelic-nette` requires PHP 7.4 or higher.

It also required `kdyby/events` package.

## Installation

The best way to install blueweb/newrelic-nette is using [Composer](http://getcomposer.org/):

```bash
$ composer require blueweb/newrelic-nette
```

You have to also add this to your `config.neon`:

```yaml
extensions:
	newrelic: Blueweb\NewRelic\DI\DiExtension
```

You can also manually disable the extension:

```yaml
newrelic:
	enable: false # default is true
```
