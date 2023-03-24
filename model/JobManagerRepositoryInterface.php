<?php


namespace Bude\JobManager\Model;


use Bude\JobManager\Job;
use Bude\JobManager\JobExecution;
use Bude\JobManager\Logger\JobLoggerEntry;

interface JobManagerRepositoryInterface
{

	/**
	 * @param Job $job
	 * @return mixed
	 */
	public function registerNewJob(Job $job);

	/**
	 * @param $jobname
	 * @return Job
	 */
	public function getJobByName($jobname);

	public function updateJob(Job $job);

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