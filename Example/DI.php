<?php
/**
 * User: bderix
 * Date: 12.07.2024
 */

namespace Bude\JobManager\Example;

use Bude\JobManager\Model;
use PDO;

class DI {

	public static function getPdo($db)
	{
		$dsn = "mysql:host={$db['hostname']};dbname={$db['database']};charset=utf8mb4";
		return new PDO($dsn, $db['login'], $db['pass']);
	}


	public static function getJobManagerModel($db)
	{
		static $jobManagerModel;
		if (!empty($jobManagerModel)) return $jobManagerModel;

		$jobManagerModel = new Model\JobManagerPdoRepository(self::getPdo($db));
		return $jobManagerModel;
	}

}