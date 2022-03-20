<?php

namespace Blueweb\NewRelicLogger\DI;

use Kdyby\Events\DI\EventsExtension;
use Nette\DI\CompilerExtension;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

class NewRelicLoggerExtension extends CompilerExtension
{
	public function getConfigSchema(): Schema
	{
		$parameters = $this->getContainerBuilder()->parameters;
		$debugMode = $parameters['debugMode'] ?? FALSE;

		return Expect::structure([
			'enabled' => Expect::bool($debugMode),
		]);
	}

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		if ($this->config->enabled) {
			$builder->addDefinition($this->prefix('listener'))
				->setType('Blueweb\NewRelicLogger\NewRelicProfilingListener')
				->addTag(EventsExtension::TAG_SUBSCRIBER);
		}
	}
}
