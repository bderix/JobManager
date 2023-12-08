<?php

namespace Bude\JobManager;

class DI {

	public static function getJobLogger($repository) {
		static $jobLogger;
		if (!empty($jobLogger)) return $jobLogger;

		// $filename = '/var/www/data/jobmanagerfilelogger.txt';
		// $jobLogger = new FileJobLogger($filename);
		$jobLogger = new Logger\JobLogger($repository);
		return $jobLogger;


	}

}