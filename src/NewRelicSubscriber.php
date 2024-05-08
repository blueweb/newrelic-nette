<?php

namespace Blueweb\NewRelic;

use Nette\Application\Application;
use Nette\Application\BadRequestException;
use Nette\Application\Request;
use Throwable;

class NewRelicSubscriber
{
	public function onRequest(
		Application $application,
		Request $request
	): void {
		if (!extension_loaded('newrelic')) {
			return;
		}

		if (PHP_SAPI === 'cli') {
			$jobName = basename($_SERVER['argv'][0]);
			$jobParams = implode(' ', array_slice($_SERVER['argv'], 1));

			newrelic_name_transaction('$ ' . $jobName . ' ' . $jobParams);
			newrelic_background_job();

			return;
		}

		$presenter = $request->getPresenterName();
		$params = $request->getParameters();
		$action = isset($params['action']) ? ':' . $params['action'] : '';

		newrelic_name_transaction($presenter . $action);
	}

	public function onError(
		Application $application,
		Throwable $e
	): void {
		if (!extension_loaded('newrelic')) {
			return;
		}

		if ($e instanceof BadRequestException) {
			return;
		}

		newrelic_notice_error($e->getMessage(), $e);
	}
}
