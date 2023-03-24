<?php
/**
 * User: bderix
 * Date: 20.03.2023
 */

namespace Bude\JobManager;


class JobMangerExceptionCodes {

	const NO_JOB = 100;
	const JOB_IS_NOT_ACTIVE = 110;
	const LAST_EXECUTION_HAS_FINISHED_WITH_ERROR = 200;
	const UNKNOWN_JOB = 120;
	const MISSING_REGISTRATION = 130;
	const NO_JOB_EXECUTION_ADDED = 140;
	const NO_EXECUTION_POSSIBLE = 150;

}