<?php

namespace Blueweb\NewRelicLogger\DI;

use Kdyby;
use Nette;

class NewRelicLoggerExtension extends Nette\DI\CompilerExtension
{

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		$config = $this->getConfig(array(
			'enabled' => !$builder->expand('%debugMode%')
		));

		Nette\Utils\Validators::assertField($config, 'enabled');

		if ($builder->expand($config['enabled'])) {
			$builder->addDefinition($this->prefix('listener'))
				->setClass('Blueweb\NewRelicLogger\NewRelicProfilingListener')
				->addTag(Kdyby\Events\DI\EventsExtension::TAG_SUBSCRIBER);
		}
	}


	public static function register(Nette\Configurator $configurator)
	{
		$configurator->onCompile[] = function ($config, Nette\DI\Compiler $compiler) {
			$compiler->addExtension('newRelic', new NewRelicLoggerExtension);
		};
	}

}
