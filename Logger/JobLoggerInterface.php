<?php

namespace Bude\JobManager\Logger;

/**
 * Ein CronLogger benötigt eine taskId, die hierüber gesetzt werden muss.
 * Interface CronLoggerInterface
 * @package Bude\Shop\Cron
 */
interface JobLoggerInterface {

	/**
	 * Every log entry of jobExecution needs a references to the id of jobExecution
	 * @param $taskId
	 * @return mixed
	 */
	public function setExecutionId($taskId);

}