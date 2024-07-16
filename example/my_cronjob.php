<?php

// this php-script is to be used to start the (cron)job, e.g. via crontab, e.g.
// */5    *    *    *    *         /var/www/scripts/cron/import/my_cronjob.php


use Bude\JobManager;
use Bude\DI;


// e.g., up to your control
$live = true;


try {

	// First: create your cronjob that will be executed via start() method below

	$fakeDb = null;
	$fakeResource = null;
	$testString = "This conjob gets tracked with the JobManager library";
	$cronjob = new MyCronJob($fakeDb, $fakeResource, $testString);



	// Next: think of a unique name of the cronjob (primary key)
	$jobname = 'mycronjob'; // up to you
	$groupname = 'imports';
	$status = JobManager\JobExecutor::ACTIVE;
	$script = 'import/my_cronjob.php';
	$minElapseOnSuccess = 1;
	$minElapseOnError = 60;
	$description = 'Imports data';
	// $options = Object;


	// Next: get the jobmanager db-model

	// $db = $pdo; // $db could be a pdo or a config array
	$db['dsn'] = "mysql:host=dbhost;dbname=dbname;charset=utf8mb4";
	$db['username'] = 'dbuser';
	$db['password'] = 'dbpass';


	$jobManagerModel = DI::getJobManagerModel();
	$jobManager = new JobManager\JobManager($jobManagerModel);

	$job = $jobManager->getJobOrRegister($jobname, new JobManager\JobExecutor($jobname, $groupname, $status, $minElapseOnSuccess, $minElapseOnError, $script, $description));
	if (!$live) $job->setOption($job::OPTION_FORCE_RESTART, true);


	// Next: get jobExecutor or register in database (if not already registered)
	$jobExecutor = $jobManager->getJob($jobname);
	if (empty($jobExecutor)) { // typically on first call
		// provide data about this (cron)job
		$groupname = 'imports'; // up to you
		$status = JobManager\JobExecutor::ACTIVE;
		$minElapseOnSuccess = 60 * 60; // seconds
		$minElapseOnError = 60 * 5; // seconds
		$script = 'my_cronjob.php';
		$description = 'just a cronjob that makes something, e.g. import some data';

		// save data in database
		$jobExecutor = new JobManager\JobExecutor($jobname, $groupname, $status, $minElapseOnSuccess, $minElapseOnError, $script, $description);
		$jobManager->registerJob($jobExecutor);
	}

	// for testing only:
	if (!$live) $jobExecutor->setOption($jobExecutor::OPTION_FORCE_RESTART, true);



	// Next: execute the (cron)job via start()-method of MyCronJob

	$origin = 'cron'; // up to you
	$jobExecutor->setExecutableTask($cronjob); // the (cron)job to be executed
	$jobManager->startExecution($jobExecutor, $origin); // execute the cronjob
	// fyi: these a two steps for future reasons


	// Next: if the (cron)job has finished, the finish time+status+... implicitly is written to database
	// @see JobExecutor::startExecution()

} catch (Exception $e) {
	echo "error on startin $jobname: " . $e->getMessage();
	print_r($e);
}
