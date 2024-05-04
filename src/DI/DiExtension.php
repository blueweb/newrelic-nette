<?php

namespace Blueweb\NewRelic\DI;

use Blueweb\NewRelic\NewRelicSubscriber;
use Kdyby\Events\DI\EventsExtension;
use Nette\DI\CompilerExtension;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

class DiExtension extends CompilerExtension
{
	public function getConfigSchema(): Schema
	{
		$parameters = $this->getContainerBuilder()->parameters;
		$debugMode = $parameters['debugMode'] ?? false;

		return Expect::structure(
			[
				'enable' => Expect::bool($debugMode),
			]
		);
	}

	public function loadConfiguration(): void
	{
		/** @var object{enable:bool} $config */
		$config = $this->getConfig();
		$builder = $this->getContainerBuilder();

		if ($config->enable) {
			$builder->addDefinition($this->prefix('subscriber'))
				->setType(NewRelicSubscriber::class)
				->addTag(EventsExtension::TAG_SUBSCRIBER);
		}
	}
}
