<?php

namespace Blueweb\NewRelicLogger;

use Tracy\Logger as TracyLogger;

class Logger extends TracyLogger
{
	public function log($message, $priority = self::INFO): ?string
	{
		$res = parent::log($message, $priority);

		if (extension_loaded('newrelic')) {
			if ($priority === self::ERROR || $priority === self::CRITICAL) {
				if (is_array($message)) {
					$message = implode(' ', $message);
				}
				newrelic_notice_error($message);
			}
		}

		return $res;
	}
}
