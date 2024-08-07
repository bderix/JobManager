<?php

// this php-script is to be used to start the (cron)job, e.g. via crontab, e.g.
// */5    *    *    *    *         /var/www/scripts/cron/import/my_cronjob.php
// or manually, e.g.
// php my_cronjob.php force

require __DIR__ . '/vendor/autoload.php';

use Bude\JobManager;


// e.g., up to your control, e.g. use it to set options on your job
$live = true;

// First: create your cronjob that hat to implement the ExecutableJobInterface, especially the start() method.

$fakeDb = null;
$fakeResource = null;
$testString = "This conjob gets tracked with the JobManager library";
$my_cronjob = new JobManager\Example\MyCronJob($fakeDb, $fakeResource, $testString);

// Next: think of a unique name of the cronjob (primary key)
$jobname = 'mycronjob'; // up to you
$groupname = 'imports';
$status = JobManager\JobExecutor::ACTIVE;
$script = 'import/my_cronjob.php';
$minElapseOnSuccess = 1; // at least 1 minute has to elapse before restart after last successfull start
$minElapseOnError = 60; // at least 60 minutes have to elapse before restart after last error
$description = 'Imports data';
// $options = Object;

try {
	$db =array(
		'hostname' => '10.64.213.228',
		'login' => 'mylogin',
		'pass' => 'xxxxxxxxxxxxxxx',
		'database' => 'log'
	);
	// Next: get the jobmanager db-model to write job parameters and executions to database.
	$jobManagerModel = JobManager\Example\DI::getJobManagerModel($db);
	$jobManager = new JobManager\JobManager($jobManagerModel);

	// create a jobExecuter that will start your cronjob according to parameters of your cronjob and options
	$jobExecutor = $jobManager->getJobOrRegister($jobname, new JobManager\JobExecutor($jobname, $groupname, $status, $minElapseOnSuccess, $minElapseOnError, $script, $description));
	$jobExecutor->setExecutableTask($my_cronjob);

	// example how to use $options
	if (!$live) $jobExecutor->setOption('nodelete', true); // you can use this options in your MyCronJob, @see ExecutableJobInterface
	if (isset($argv[1]) and $argv[1] == 'force') $force = true;
	else $force = false;
	if ($force) $jobExecutor->setOption(JobManager\JobExecutor::OPTION_FORCE_RESTART, true);
	$origin = 'manual'; // or 'cron' or what you want

	// now start the (cron)job by calling start()-method of MyCronJob
	$jobManager->startExecution($jobExecutor, $origin);

	// Next: if the (cron)job has finished, the finish time+status+... implicitly is written to database
	// @see JobExecutor::startExecution()

} catch (Exception $e) {
	echo "error on starting $jobname: " . $e->getMessage();
	print_r($e);
}
