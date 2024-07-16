<?php

namespace Bude\JobManager;

use Bude\JobManager\Logger;

/**
 * Klassen die dieses Interface implementieren können z.B. als Job gesetzt werden.
 * @package Bude\Shop\Cron
 * @see JobExecutor
 */
interface ExecutableJobInterface {

	public function start();

	public function end();

	public function getCode();

	public function setCode(int $code);

	/**
	 * Für den Logger muss der Job eine Zusammenfassung bereitstellen.
	 * @return string
	 */
	public function getMessage();

	public function setMessage(string $message);

	public function getOption(string $name);

	public function setOption(string $name, $value);

	public function hasOption(string $name);

	public function setLogger(Logger\LoggerInterface $logger);


}