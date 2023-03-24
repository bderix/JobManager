<?php

namespace Bude\JobManager;

use Bude\JobManager\JobLoggerInterface;
use InvalidArgumentException;

/**
 * Holds cronjob data.
 * @package Bude\Shop\Cron
 */
class JobExecution {

	const STARTED = 'started';
	const SUCCESS = 'success';

	/**
	 * The executionId is generated on first execution, typically an autogenerated id from database.
	 * @var
	 */
	protected $executionId;

	/**
	 * The jobname references the job in Job::jobname.
	 * @var string
	 */
	protected $jobname;

	protected $status;

	protected $origin;

	protected $start;

	protected $finish;

	protected $code;

	protected $summary;

	/**
	 * Logger instance for logging job execution
	 * @var JobLoggerInterface
	 */
	protected $logger;



	public function __construct(string $jobname, string $status, string $origin, string $start = null, string $finish = null, int $code = 0, string $summary = null)
	{
		if (empty($jobname)) throw new InvalidArgumentException('Empty jobname');
		$this->jobname = $jobname;
		$this->status = $status;
		$this->origin = $origin;
		$this->start = $start;
		$this->finish = $finish;
		$this->code = $code;
		$this->summary = $summary;
	}

	/**
	 * @return int
	 */
	public function getExecutionId() {
		return $this->executionId;
	}

	/**
	 * @param int $executionId
	 */
	public function setExecutionId($executionId) {
		$this->executionId = $executionId;
	}

	public function getOrigin() {
		return $this->origin;
	}

	public function getSummary() {
		return $this->summary;
	}

	public function setSummary($summary) {
		$this->summary = $summary;
	}

	/**
	 * @return string
	 */
	public function getJobname() {
		return $this->jobname;
	}

	/**
	 * @return string
	 */
	public function getStart() {
		return $this->start;
	}

	/**
	 * @return string
	 */
	public function getFinish() {
		return $this->finish;
	}

	/**
	 * @return int
	 */
	public function getCode() {
		return $this->code;
	}

	/**
	 * @param int $code
	 */
	public function setCode($code) {
		$this->code = $code;
	}

	public function jobFinishedSuccessful() {
		return $this->status == self::SUCCESS;
	}

	public function elapsedSecondsSinceLastStart() {
		if (empty($this->start)) return 0;
		$time = strtotime($this->start);
		if ($time) return (time() - $time);
		else return 0;
	}

	public function elapsedSecondsSinceLastFinish() {
		if (empty($this->finish)) return 0;
		$time = strtotime($this->finish);
		if ($time) return (time() - $time);
		else return 0;
	}

	public function __toString() {
        return "{$this->executionId}: {$this->jobname} (start: {$this->start})";
    }

}
