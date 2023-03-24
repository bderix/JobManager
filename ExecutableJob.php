<?php
/**
 * User: bderix
 * Date: 15.03.2023
 */

namespace Bude\JobManager;

/**
 * Any job or task that can be started. Standalone or registered by CronjobManager and started by CronjobManager.
 * Class ExecutableJob
 * @package Bude\Shop\Cron
 */
class ExecutableJob implements ExecutableJobInterface {

	/**
	 * @LoggerInterface logger
	 */
	protected $logger;

	protected $code = '';

	protected $message = '';

	protected $options;

	public function start() {
	}

	public function end() {
	}

	public function setLogger(LoggerInterface $logger) {
		$this->logger = $logger;
	}

	public function getCode() {
		return $this->code;
	}

	/**
	 * @param string $code
	 */
	public function setCode(int $code) {
		$this->code = $code;
	}

	public function getMessage() {
		return $this->message;
	}

	public function setMessage(string $message) {
		$this->message = $message;
	}

	public function getOption(string $name) {
		if ($this->hasOption($name)) return $this->options[$name];
		else return '';
	}

	public function setOption(string $name, $value) {
		$this->options[$name] = $value;
	}

	public function hasOption(string $name) {
		if (empty($this->options[$name])) return false;
		else return true;
	}



}