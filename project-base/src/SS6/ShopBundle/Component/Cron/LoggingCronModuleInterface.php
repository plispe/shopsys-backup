<?php

namespace SS6\ShopBundle\Component\Cron;

use Symfony\Bridge\Monolog\Logger;

interface LoggingCronModuleInterface {

	/**
	 * @param \Symfony\Bridge\Monolog\Logger $logger
	 */
	public function setLogger(Logger $logger);

}