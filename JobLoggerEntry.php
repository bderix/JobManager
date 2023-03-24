<?php

namespace Bude\JobManager;

use InvalidArgumentException;

/**
 * Class JobLoggerEntry
 * @package Bude\JobManager\DataObject
 */
class JobLoggerEntry {

	public $executionId;

	public $step;

	public $message;

	public $level;

	public $created;

	/**
	 * JobLoggerEntry constructor.
	 * @param $jobId
	 * @param $step
	 * @param $info
	 * @param $level
	 * @param $created
	 */
	public function __construct($executionId, $step, $level, $message)
	{
		if (empty($executionId)) throw new InvalidArgumentException('Empty jobId');
		if (empty($step)) throw new InvalidArgumentException('Empty step');
		$this->executionId = $executionId;
		$this->step = $step;
		$this->message = $message;
		$this->level = $level;
	}


}