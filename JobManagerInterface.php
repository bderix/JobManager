<?php

namespace Bude\JobManager;

/**
 * Interface für CronJobs.
 * @package Bude\Shop\Cron
 */
interface JobManagerInterface {

	/**
	 * Check if job is already regsitered.
	 * @param string $jobname
	 * @return bool
	 */
	public function isJobRegistered(string $jobname);

	/**
	 * Before executing, a job must first be registered.
	 * @param JobExecutor $job
	 * @return mixed
	 */
	public function registerJob(JobExecutor $job);

	/**
	 * @param string $jobname
	 * @return JobExecutor
	 */
	public function getJob(string $jobname);

	/**
	 * Check if the cronjob can be started and start the registered task if it is allowed.
	 * @param JobExecutor $job
	 * @param string $origin
	 * @return mixed
	 */
	public function startExecution(JobExecutor $job, string $origin = '');

	/**
	 * Gets the last execution of the job $jobname.
	 * @param string $jobname The name of the job.
	 * @return JobExecution The last started job.
	 * @see JobManagerRepositoryInterface::getLastExecution()
	 */
	public function getLastJobExecution(string $jobname);


}