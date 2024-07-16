<?php
/**
 * User: bderix
 * Date: 12.07.2024
 */

namespace bude\jobmanager\example;

class DI {

	public static function getModel($name)
	{
		$model = \activerecord_activeRecordFactory::get_instance()->get($name);
		return $model;
	}

	public static function getPdo($database)
	{
		$db = \config_web::$dbase[$database];
		$dsn = "mysql:host={$db['hostname']};dbname={$db['database']};charset=utf8mb4";
		return new PDO($dsn, $db['login'], $db['pass']);
	}


	public static function getJobManagerModel()
	{
		static $jobManagerModel;
		if (!empty($jobManagerModel)) return $jobManagerModel;

		$jobManagerModel = new Model\JobManagerPdoRepository(self::getPdo('log'));
		return $jobManagerModel;
	}

}