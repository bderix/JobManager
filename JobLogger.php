<?php


namespace Bude\JobManager;


/**
 * Every JobLogger must be derived from this mutli interface class and then simply implement the log() method.
 * @package Bude\Shop\Cron
 */
class JobLogger implements LoggerInterface, JobLoggerInterface {

	protected $executionId;

	protected $stepId;

	protected $model;

	protected $logLevel = LogLevel::INFO;

	public function __construct(Model\JobManagerRepositoryInterface $model) {
		$this->model = $model;
	}

	public function setLoglevel($level) {
		$this->logLevel = $level;
	}

	public function setExecutionId($executionId) {
		$this->executionId = $executionId;
	}

	public function emergency($message, array $context = array()) {
		$this->log(LogLevel::EMERGENCY, $message, $context);
	}

	public function alert($message, array $context = array()) {
		$this->log(LogLevel::ALERT, $message, $context);
	}

	public function critical($message, array $context = array()) {
		$this->log(LogLevel::CRITICAL, $message, $context);
	}

	public function error($message, array $context = array()) {
		$this->log(LogLevel::ERROR, $message, $context);
	}

	public function warning($message, array $context = array()) {
		$this->log(LogLevel::WARNING, $message, $context);
	}

	public function notice($message, array $context = array()) {
		$this->log(LogLevel::NOTICE, $message, $context);
	}

	public function info($message, array $context = array()) {
		$this->log(LogLevel::INFO, $message, $context);
	}

	public function debug($message, array $context = array()) {
		$this->log(LogLevel::DEBUG, $message, $context);
	}

	public function log($level, $message, array $context = array()) {
		if ($this->logLevel == LogLevel::INFO and $level == LogLevel::DEBUG) return;
		if (empty($this->executionId)) return false;
		$this->stepId++;
		$log = new JobLoggerEntry($this->executionId, $this->stepId, $level, $message);
		$this->model->saveJobLogEntry($log);
	}

}
