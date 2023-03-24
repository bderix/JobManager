<?php
/**
 * User: bderix
 * Date: 10.03.2023
 */

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
	 * @param Job $job
	 * @return mixed
	 */
	public function registerJob(Job $job);

	/**
	 * @param string $jobname
	 * @return Job
	 */
	public function getJob(string $jobname);

	/**
	 * Check if the cronjob can be started and start the registered task if it is allowed.
	 * @param Job $job
	 * @param string $origin
	 * @return mixed
	 */
	public function startExecution(Job $job, string $origin = '');

	/**
	 * Gets the last execution of the job $jobname.
	 * @param string $jobname The name of the job.
	 * @return JobExecution The last started job.
	 * @see JobManagerRepositoryInterface::getLastExecution()
	 */
	public function getLastJobExecution(string $jobname);


}