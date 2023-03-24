<?php

namespace Bude\JobManager;

class DI {

	/**
	 *
	 * @param $db can be a config-array or a pdo resource.
	 * @return Model\JobManagerPdoRepository|mixed
	 */
	public static function getJobManagerModel($db)
	{
		static $jobManagerModel;
		if (!empty($jobManagerModel)) return $jobManagerModel;

		$jobManagerModel = new Model\JobManagerPdoRepository($db);
		return $jobManagerModel;
	}

	public static function getJobLogger() {
		static $jobLogger;
		if (!empty($jobLogger)) return $jobLogger;

		// $filename = '/var/www/data/jobmanagerfilelogger.txt';
		// $jobLogger = new FileJobLogger($filename);
		$jobLogger = new Logger\JobLogger(self::getJobManagerModel());
		return $jobLogger;


	}

}