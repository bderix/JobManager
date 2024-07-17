<?php

namespace Bude\JobManager\Example;

use Bude\JobManager\ExecutableJob;


/**
 * Example class that does soemthing.
 * This class is called via the (cron)job-script
 */
class MyCronJob extends ExecutableJob {

	private $dbForMyCronJob;

	private $anyResource;

	private $testString;

	public function __construct($dbForMyCronJob, $anyResource, $testString) {
		$this->dbForMyCronJob = $dbForMyCronJob;
		$this->anyResource = $anyResource;
		$this->testString = $testString;
	}

	public function start() {

		if (empty($this->testString)) {
			$this->logger->error("no testString");
		}

		$this->logger->notice('MyCronJob startet');

		// do something
		$calc = 2 + 2;

		$this->logger->info('calculation finished');

		if ($calc != 4) {
			$this->logger->warning('wrong result');
		}

		$this->logger->notice('calc is ' . $calc);

		if ($this->hasOption('nodelete')) {
			// example how to use options
		}

		$this->setMessage('this is my finish message');
		$this->setCode(200); // any code you want

	}


	public function end() {
		// you don't need to do soemthing here
	}
}