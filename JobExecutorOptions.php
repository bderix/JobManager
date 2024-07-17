<?php

namespace Bude\JobManager;

enum JobExecutorOptions: string {

	/**
	 * Option value to restart the job, no matter any other parameters.
	 */
	case OPTION_FORCE_RESTART = 'force-restart';
}
