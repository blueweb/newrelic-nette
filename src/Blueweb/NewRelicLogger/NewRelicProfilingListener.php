<?php

namespace Blueweb\NewRelicLogger;

use Kdyby\Events\Subscriber;
use Nette\Application\Application;
use Nette\Application\BadRequestException;
use Nette\Application\Request;
use Nette\SmartObject;
use Throwable;

class NewRelicProfilingListener implements Subscriber
{
	use SmartObject;

	public function getSubscribedEvents(): array
	{
		return [
			'Nette\\Application\\Application::onStartup',
			'Nette\\Application\\Application::onRequest',
			'Nette\\Application\\Application::onError',
		];
	}

	public function onStartup(Application $app): void
	{
		if (!extension_loaded('newrelic')) {
			return;
		}
	}

	public function onRequest(Application $app, Request $request): void
	{
		if (!extension_loaded('newrelic')) {
			return;
		}

		if (PHP_SAPI === 'cli') {
			$jobName = basename($_SERVER['argv'][0]);
			$jobParams = implode(' ', array_slice($_SERVER['argv'], 1));

			newrelic_name_transaction('$ ' . $jobName . ' ' . $jobParams);
			newrelic_background_job(TRUE);

			return;
		}

		$presenter = $request->getPresenterName();
		$params = $request->getParameters();
		$action = isset($params['action']) ? ':' . $params['action'] : '';

		newrelic_name_transaction($presenter . $action);
	}

	public function onError(Application $app, Throwable $e): void
	{
		if (!extension_loaded('newrelic')) {
			return;
		}

		if ($e instanceof BadRequestException) {
			return;
		}

		newrelic_notice_error($e->getMessage(), $e);
	}
}
