<?php

namespace Bude\JobManager;


/**
 * Easy file based logger for logging of executed ExecutableJobs.
 * Class FileJobLogger
 * @package Bude\JobManager
 * @see ExecutableJob
 */
class FileJobLogger extends JobLogger {

	protected $filename;


	public function __construct($filename) {
		if ( !file_exists($filename) ) {
   			throw new Exception('file not found.');
		}
		$this->filename = $filename;
	}

	public function log($level, $message, array $context = array()) {
		$data = "{$level}:\t\t{$message}" . PHP_EOL;
		$fp = fopen($this->filename, 'a');
		if (!$fp) {
			throw new \Exception('file open failed');
		}
		fwrite($fp, $data);
	}

}