<?php

namespace Blueweb\NewRelic\DI;

use Blueweb\NewRelic\NewRelicSubscriber;
use Nette\DI\CompilerExtension;
use Nette\PhpGenerator\ClassType;
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
				'enable' => Expect::bool(!$debugMode),
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
				->setType(NewRelicSubscriber::class);
		}
	}

	public function afterCompile(ClassType $class): void
	{
		/** @var object{enable:bool} $config */
		$config = $this->getConfig();
		$builder = $this->getContainerBuilder();

		if ($config->enable) {
			$subscriberServiceName = $this->prefix('subscriber');
			$initialize = $class->getMethod('initialize');
			$initialize->addBody(
				$builder->formatPhp(
					<<<PHP
\$this->getService(?)->onRequest[] = [\$this->getService(?), ?];
\$this->getService(?)->onError[] = [\$this->getService(?), ?];
PHP,
					[
						'application',
						$subscriberServiceName,
						'onRequest',
						'application',
						$subscriberServiceName,
						'onError',
					]
				)
			);
		}
	}
}
