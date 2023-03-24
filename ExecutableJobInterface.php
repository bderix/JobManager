<?php

namespace Bude\JobManager;

/**
 * Klassen die dieses Interface implementieren k�nnen z.B. als Job gesetzt werden.
 * @package Bude\Shop\Cron
 * @see Job
 */
interface ExecutableJobInterface {

	public function start();

	public function end();

	public function getCode();

	public function setCode(int $code);

	/**
	 * F�r dne Logger muss der Job eine Zusammenfassung bereitstellen.
	 * @return string
	 */
	public function getMessage();

	public function setMessage(string $message);

	public function getOption(string $name);

	public function setOption(string $name, $value);

	public function hasOption(string $name);

	public function setLogger(LoggerInterface $logger);


}