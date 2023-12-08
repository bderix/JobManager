<?php

namespace Bude\JobManager;

use Bude\JobManager\JobExecutor;
use Exception;
use InvalidArgumentException;

/**
 * JobManager class for registering and executing (cron)jobs.
 * Use this class to control and track the executions of your (cron)jobs, e.g. prevent duplicate execution, get an overview
 * of all executed (cron)jobs, add a Logger to every (cron)job.
 * @package Bude\JobManager
 * @author Bernhard Derix <bderix at web de>
 */
class JobManager
{

	/**
	 * @Job[]
	 */
	private $jobs;

	/**
	 * JobModel instance for managing jobs in a database
	 * @var Model\JobManagerRepositoryInterface
	 */
	protected $jobModel;

	protected $options;

	/**
	 * Constructor for the JobManager class.
	 * @param JobExecutor $jobInfos some attributes of the job, e.g. jobname and start date.
	 * @param Model\JobManagerRepositoryInterface $jobModel gateway to save data, typically in a database.
	 */
	public function __construct(Model\JobManagerRepositoryInterface $jobModel)
	{
		if (empty($jobModel)) throw new InvalidArgumentException('empty jobmodel');
		$this->jobModel = $jobModel;
	}


	protected function addJob(JobExecutor $job)
	{
		$jobname = $job->getJobname();
		$this->jobs[$jobname] = $job;
	}

	/**
	 * @param string $jobname
	 * @return \Bude\JobManager\JobExecutor|null
	 */
	public function getJob(string $jobname)
	{
		if (isset($this->jobs[$jobname])) {
			return $this->jobs[$jobname];
		}
		else {
			$job = $this->jobModel->getJobByName($jobname);
			if (!empty($job)) {
				$this->addJob($job);
				return $job;
			}
			return null;
		}
	}

	public function getJobOrRegister(string $jobname, JobExecutor $jobData)
	{
		$job = $this->getJob($jobname);
		if (empty($job)) {
			$this->registerJob($jobData);
		}
		return $job;
	}

	public function isJobRegistered(string $jobname)
	{
		$job = $this->getJob($jobname);
		if (empty($job)) return false;
		else return true;
	}

	public function registerJob(JobExecutor $job)
	{
		if (empty($job)) throw new InvalidArgumentException('no job to register');
		if ($this->isJobRegistered($job->getJobname())) return;
		$this->jobModel->registerNewJob($job);
		$this->addJob($job);
	}


	public function startExecution(JobExecutor $job, string $origin = '')
	{
		$jobname = $job->getJobname();

		// Check if the job can be restarted
		if (!$job->hasOption($job::OPTION_FORCE_RESTART)) {
			if (!$job->isActive()) {
				throw new Exception("job is not active", JobMangerExceptionCodes::JOB_IS_NOT_ACTIVE);
			}

			$lastJobExecution = $this->getLastJobExecution($jobname);
			if (!empty($lastJobExecution)) {
				// if job finished successfully next execution has to wait a defined min time befor next start
				if ($lastJobExecution->jobFinishedSuccessful() and $job->minElapseOnSuccess!=0 and $lastJobExecution->elapsedSecondsSinceLastFinish() < $job->minElapseOnSuccess) {
					throw new Exception("not enough seconds have elapsed before next execution ({$job->minElapseOnSuccess} seconds needed)", JobMangerExceptionCodes::LAST_EXECUTION_HAS_FINISHED_WITH_ERROR);
				}
				// if job hasn't finished successfully he has to wait a defined min time befor next start
				if (!$lastJobExecution->jobFinishedSuccessful() and $lastJobExecution->elapsedSecondsSinceLastStart() < $job->minElapseOnError) {
					throw new Exception("last execution hasn't finisheed successfully", JobMangerExceptionCodes::LAST_EXECUTION_HAS_FINISHED_WITH_ERROR);
				}
			}
		}

		$job->startExecution($this->jobModel, $origin);
	}


	public function getLastJobExecution(string $jobname)
	{
		return $this->jobModel->getLastExecution($jobname);
	}

	// protected function hasOption($name) {
	// 	if (empty($this->options[$name])) return false;
	// 	else return true;
	// }


}