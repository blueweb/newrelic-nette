<?php

namespace Blueweb\NewRelicLogger;

use Kdyby;
use Nette;
use Nette\Application\Application;
use Nette\Application\Request;
use Tracy\Debugger;
use Tracy\Logger;

class NewRelicProfilingListener implements Kdyby\Events\Subscriber
{

	use Nette\SmartObject;

	public function getSubscribedEvents()
	{
		return array(
			'Nette\\Application\\Application::onStartup',
			'Nette\\Application\\Application::onRequest',
			'Nette\\Application\\Application::onError',
		);
	}


	public function onStartup(Application $app)
	{
		if (!extension_loaded('newrelic')) {
			return;
		}

		$oldLogger = Debugger::getLogger();

		$logger = new Logger($oldLogger->directory, $oldLogger->email);
		$logger->emailSnooze = $oldLogger->emailSnooze;
		$logger->mailer = $oldLogger->mailer;

		Debugger::setLogger($logger);
	}


	public function onRequest(Application $app, Request $request)
	{
		if (!extension_loaded('newrelic')) {
			return;
		}

		if (PHP_SAPI === 'cli') {
			newrelic_name_transaction('$ ' . basename($_SERVER['argv'][0]) . ' ' . implode(' ', array_slice($_SERVER['argv'], 1)));

			newrelic_background_job(TRUE);

			return;
		}

		$params = $request->getParameters();
		newrelic_name_transaction($request->getPresenterName() . (isset($params['action']) ? ':' . $params['action'] : ''));
	}


	public function onError(Application $app, \Exception $e)
	{
		if (!extension_loaded('newrelic')) {
			return;
		}

		if ($e instanceof Nette\Application\BadRequestException) {
			return;
		}

		newrelic_notice_error($e->getMessage(), $e);
	}

}
