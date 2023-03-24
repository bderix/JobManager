<?php

namespace Bude\JobManager\Exceptions;

/**
 * User: bderix
 * Date: 20.03.2023
 */
class JobManagerMissingRegistration extends \Exception {

	public function __construct(\Exception $e) {
		parent($e);
	}

}