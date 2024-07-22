<?php


namespace Bude\JobManager\Model;


use Bude\JobManager\JobExecutor;
use Bude\JobManager\JobExecution;
use Bude\JobManager\Logger\JobLoggerEntry;

interface JobManagerRepositoryInterface
{

	/**
	 * @param JobExecutor $job
	 * @return mixed
	 */
	public function registerNewJob(JobExecutor $job);

	/**
	 * @param $jobname
	 * @return JobExecutor
	 */
	public function getJobByName($jobname);

	public function updateJob(JobExecutor $job);

	public function updateJobStatus($jobname, $status);

	public function setJobStarted(JobExecution $job);

	public function setJobFinished(JobExecution $jobExecution);

	public function saveJobLogEntry(JobLoggerEntry $job);

	/**
	 * @param $jobname
	 * @return JobExecution
	 */
	public function getLastExecution($jobname);

	public function getJobsExecuted($hours = 0, $jobname = '%', $groupname = '%');

	public function getJobLogs($execution_id);

	public function generateExecutableSql($sql, $params);

	public function getSql();


}