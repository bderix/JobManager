<?php

namespace Bude\JobManager;

class DI {

	public static function getJobManagerModel()
	{
		static $jobManagerModel;
		if (!empty($jobManagerModel)) return $jobManagerModel;

		$db = \config_web::$dbase['log'];
		$conn['dsn'] = "mysql:host={$db['hostname']};dbname={$db['database']};charset=utf8mb4";
		$conn['username'] = $db['login'];
		$conn['password'] = $db['pass'];
		$jobManagerModel = new Model\JobManagerPdoRepository($conn);
		return $jobManagerModel;
	}

	public static function getJobLogger() {
		static $jobLogger;
		if (!empty($jobLogger)) return $jobLogger;

		$filename = '/var/www/data/jobmanagerfilelogger.txt';
		// $jobLogger = new FileJobLogger($filename);
		$jobLogger = new JobLogger(self::getJobManagerModel());
		return $jobLogger;


	}

}