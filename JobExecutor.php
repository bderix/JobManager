<?php

namespace Bude\JobManager;

use Bude\JobManager;
use Exception;
use InvalidArgumentException;

/**
 * Every instance of JobExecutor is linked with a jobtask (of type ExecutableJob) that is to be executed by this.
 * It is used in JobManager.
 * @see JobManager\JobManager
 *
 */
class JobExecutor {

	const ACTIVE = 'active';
	const INACTIVE = 'inactive';

	/**
	 * Option value to restart the job, no matter any other parameters.
	 */
	const OPTION_FORCE_RESTART = 'force-restart';

	/**
	 * @var string The name of the job which is a primary key
	 */
	protected $jobname;

	/**
	 * @var string The group that the job belongs to
	 */
	public $jobgroup;

	/**
	 * @var string The status of the job (active or inactive)
	 */
	public $jobstatus;

	/**
	 * @var int The time in seconds a successfully finished job has to wait before next execution
	 */
	public $minElapseOnSuccess;

	/**
	 * @var int The time in seconds a not successfully finished job has to wait before next execution
	 */
	public $minElapseOnError;

	/**
	 * @var string The script that executes the job
	 */
	public $script;

	/**
	 * @var string A brief description of what the job does
	 */
	public $description;

	public 	$xml;

	/**
	 * @var array Some Options to be set externally
	 */
	protected $options;

	/**
	 * @var JobManager\ExecutableJobInterface The jobtaks belonging to this job, that should be executed.
	 */
	protected $executableTask;

	/**
	 * Job constructor.
	 *
	 * @param string $jobname The name of the job
	 * @param string $jobgroup The group that the job belongs to
	 * @param string $jobstatus The status of the job (active or inactive)
	 * @param string $script The script that executes the job
	 * @param int $minElapse
	 * @param string $description A brief description of what the job does
	 */
	public function __construct(
		string $jobname,
		string $jobgroup = '',
		string $jobstatus = self::ACTIVE,
		int    $minElapseOnSuccess = 0,
		int    $minElapseOnError = 0,
		string $script = '',
		string $description = '',
		string $xml = '')
	{
		if (empty($jobname)) throw new InvalidArgumentException("empty jobname");
		if (!in_array($jobstatus, [self::ACTIVE, self::INACTIVE])) {
			throw new InvalidArgumentException("invalid jobstatus: {$jobstatus}");
		}
		$this->jobname = $jobname;
		$this->jobgroup = $jobgroup;
		$this->jobstatus = $jobstatus;
		$this->minElapseOnSuccess = $minElapseOnSuccess;
		$this->minElapseOnError = $minElapseOnError;
		$this->script = $script;
		$this->description = $description;
		$this->xml = $xml;
	}

	/**
	 * @return string The name of the job.
	 */
	public function getJobname() {
		return $this->jobname;
	}

	public function isActive() {
		return $this->jobstatus == self::ACTIVE;
	}

	/**
	 * Sets the jobtask the job should execute
	 * @param JobManager\ExecutableJobInterface $task
	 * @return void
	 */
	public function setExecutableTask(JobManager\ExecutableJobInterface $task) {
		$this->executableTask = $task;
	}

	/**
	 * Start the linked jobtask, set start time and set the logger for the jobtask.
	 * @param JobManager\Model\JobManagerRepositoryInterface $repository
	 * @param string $origin
	 * @return void
	 * @throws Exception
	 */
	public function startExecution(JobManager\Model\JobManagerRepositoryInterface $repository, string $origin = '') {
		$execution = new JobExecution($this->jobname, JobExecution::STARTED, $origin);

		// Start the job and get its ID
		$executionId = $repository->setJobStarted($execution);

		// Throw an exception if the job ID is empty
		if (empty($executionId)) {
			throw new Exception("error running cronjob {$this->jobname}");
		}
		$execution->setExecutionId($executionId);

		// Set the logger for the task
		$jobLogger = JobManager\DI::getJobLogger($repository);
		$jobLogger->setExecutionId($executionId);
		$this->executableTask->setLogger($jobLogger);
		$this->executableTask->start();

		// end
		$this->executableTask->end();
		$execution->setCode($this->executableTask->getCode());
		$execution->setSummary($this->executableTask->getMessage());
		$repository->setJobFinished($execution);
	}


	public function hasOption(string $name) {
		if (empty($this->options[$name])) return false;
		else return true;
	}

	public function setOption(string $name, $value) {
		$this->options[$name] = $value;
	}


}

