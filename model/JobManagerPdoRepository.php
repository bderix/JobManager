<?php

namespace Bude\JobManager\Model;

use Bude\JobManager\JobExecutor;
use Bude\JobManager\JobExecution;
use Bude\JobManager\Logger\JobLoggerEntry;
use PDO;
use Exception;
use InvalidArgumentException;

/**
 * Handles all persistance requests via PHP PDO interface.
 * @package Bude\JobManager
 * @author Bernhard Derix <bderix at web de>
 */
class JobManagerPdoRepository implements JobManagerRepositoryInterface  {
	/**
	 * @var PDO
	 */
	protected $pdo;

	/**
	 * @var array
	 */
	protected $config;

	/**
	 * Holds executable SQL for debug reasons.
	 * @var
	 */
	public $sql;

	/**
	 * @param mixed $connection
	 * @param array $config
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct($connection, $config = array())
	{
		if (!$connection instanceof PDO) {
			if (empty($connection)) {
				throw new InvalidArgumentException('empty PDO connection string');
			}
			if (is_string($connection)) {
				$connection = array('dsn' => $connection);
			}
			if (!is_array($connection)) {
				throw new InvalidArgumentException('First argument to OAuth2\Storage\Pdo must be an instance of PDO, a DSN string, or a configuration array');
			}
			if (!isset($connection['dsn'])) {
				throw new InvalidArgumentException('configuration array must contain "dsn"');
			}
			$connection = array_merge(array('username' => null, 'password' => null, 'options' => array()), $connection);
			$connection = new PDO($connection['dsn'], $connection['username'], $connection['password'], $connection['options']);

		}
		$this->pdo = $connection;

		$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$this->config = array_merge(array(
			'jobs_table' => 'jobmanager_jobs',
			'jobs_executions_table' => 'jobmanager_executions',
			'jobs_logs_table' => 'jobmanager_logs',
		), $config);
	}

	public function registerNewJob(JobExecutor $job)
	{
		if (empty($job)) throw new InvalidArgumentException("no job");
		if ($this->getJobByName($job->getJobname())) throw new Exception("job already exists");
		$sql = 'INSERT INTO %s (jobname, jobgroup, jobstatus, min_elapse_on_success, min_elapse_on_error, script, description, xml) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
		$sql = sprintf($sql, $this->config['jobs_table']);
		$params = array($job->getJobname(), $job->jobgroup, $job->jobstatus, $job->minElapseOnSuccess, $job->minElapseOnError, $job->script, $job->description, $job->xml);
		return $this->execute($sql, $params);
	}

	/**
	 * @param $jobname
	 * @return JobExecutor|null
	 */
	public function getJobByName($jobname)
	{
		$sql = 'SELECT * from %s WHERE jobname like ? ';
		$sql = sprintf($sql, $this->config['jobs_table']);
		$params = array($jobname);
		$arr = $this->fetch($sql, $params);
		if (empty($arr)) return null;
		$job = new JobExecutor($arr['jobname'], $arr['jobgroup'], $arr['jobstatus'], $arr['min_elapse_on_success'], $arr['min_elapse_on_error'], $arr['script'], $arr['description'], $arr['xml']);
		return $job;
	}

	public function updateJob(JobExecutor $job)
	{
		if (!$this->getJobByName($job->getJobname())) throw new Exception('no job exists');
		$sql = 'UPDATE %s SET jobgroup=?, jobstatus=?, min_elapse_on_success=?, min_elapse_on_error=?, script=?, description=?, xml=? WHERE jobname=?';
		$sql = sprintf($sql, $this->config['jobs_table']);
		$params = array($job->jobgroup, $job->jobstatus, $job->minElapseOnSuccess, $job->minElapseOnError, $job->script, $job->description, $job->xml, $job->getJobname());
		return $this->execute($sql, $params);
	}

	public function updateJobStatus($jobname, $status)
	{
		if (!$this->getJobByName($jobname)) throw new Exception('no job exists');
		$sql = 'UPDATE %s SET status=? WHERE jobname=?';
		$sql = sprintf($sql, $this->config['jobs_table']);
		$params = array($status, $jobname);
		return $this->execute($sql, $params);
	}

	public function setJobStarted(JobExecution $job)
	{
		$sql = sprintf("INSERT INTO %s (jobname, status, origin) VALUES (?, ?, ?)", $this->config['jobs_executions_table']);
		$this->execute($sql, [$job->getJobname(), JobExecution::STARTED, $job->getOrigin()]);
		return $this->pdo->lastInsertId();
	}

	public function setJobFinished(JobExecution $jobExecution)
	{
		$sql = sprintf("UPDATE %s SET finish=NOW(), status=?, code=?, summary=? where execution_id=?", $this->config['jobs_executions_table']);
		$this->execute($sql, [JobExecution::SUCCESS, $jobExecution->getCode(), $jobExecution->getSummary(), $jobExecution->getExecutionId()]);
	}

	public function saveJobLogEntry(JobLoggerEntry $log)
	{
		$sql = sprintf("INSERT INTO %s (execution_id, step, message, level) VALUES (?, ?, ?, ?)", $this->config['jobs_logs_table']);
		return $this->execute($sql, [$log->executionId, $log->step, $log->message, $log->level]);
	}

	/**
	 * @param $jobname
	 * @return JobExecution|null
	 */
	public function getLastExecution($jobname)
	{
		$sql = sprintf("SELECT * FROM %s WHERE jobname = ? ORDER BY execution_id desc LIMIT 1", $this->config['jobs_executions_table']);
		$result = $this->fetch($sql, [$jobname]);
		if ($result) {
			$cron = new JobExecution($result['jobname'], $result['status'], $result['origin'], $result['start'], $result['finish'], $result['code'], $result['summary']);
			$cron->setExecutionId($result['execution_id']);
			return $cron;
		}
		else return null;

	}

	public function getJobs()
	{
		$sql = 'SELECT * from %s WHERE 1=1 ORDER BY jobname';
		$sql = sprintf($sql, $this->config['jobs_table']);
		return $this->fetchAll($sql);
	}

	/**
	 * @param string $client_id
	 * @return array|mixed
	 */
	public function getJobsExecuted($hours = 0, $jobname = '%', $groupname = '%')
	{
		if (!empty($hours) and !is_int($hours)) throw new InvalidArgumentException('invalid hour value');
		$sql = 'SELECT * from %s LEFT JOIN %s USING (jobname) WHERE start > NOW() - INTERVAL ? HOUR AND jobname like ? AND jobgroup like ? ORDER BY execution_id desc';
		$sql = sprintf($sql, $this->config['jobs_executions_table'], $this->config['jobs_table']);
		$params = array($hours, $jobname, $groupname);
		return $this->fetchAll($sql, $params);
	}

	public function getJobLogs($execution_id)
	{
		if (empty($execution_id) or !is_int($execution_id)) throw new InvalidArgumentException("invalid execution_id");
		$sql = 'SELECT * from %s WHERE execution_id like ? ORDER BY step';
		$sql = sprintf($sql, $this->config['jobs_logs_table']);
		$params = array($execution_id);
		return $this->fetchAll($sql, $params);
	}

	public function generateExecutableSql($sql, $params)
	{
		$pos = 0;
	    foreach ($params as $value) {
	        $quotedValue = is_numeric($value) ? $value : "'" . addslashes($value ?? '') . "'";
			$pos = strpos($sql, '?', $pos);
			$sql = substr_replace($sql, $quotedValue, $pos, 1);
	        // $sqlWithParams = str_replace(':' . $key, $quotedValue, $sqlWithParams);
	    }
	    return $sql;
	}

	public function getErrorMessage() {
		return $stmt->errorInfo;

	}

	/**
	 * @return string
	 */
	public function getSql() {
		return $this->sql;
	}

	private function fetch($sql, $params = array())
	{
		$this->pdo->prepare($sql);
		$this->sql = $this->generateExecutableSql($sql, $params);
		$stmt = $this->execute($sql, $params);
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	private function fetchAll($sql, $params = array())
	{
		$this->pdo->prepare($sql);
		$this->sql = $this->generateExecutableSql($sql, $params);
		$stmt = $this->execute($sql, $params);
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	private function execute($sql, $params = array())
	{
		$stmt = $this->pdo->prepare($sql);
		$this->sql = $this->generateExecutableSql($sql, $params);
		$ok = $stmt->execute($params);
		if (!$ok) throw new Exception($stmt->errorInfo(), $stmt->errorCode());
		else return $stmt;
	}



	/**
	 * DDL to create OAuth2 database and tables for PDO storage
	 *
	 * @see https://github.com/dsquier/oauth2-server-php-mysql
	 *
	 * @param string $dbName
	 * @return string
	 */
	public function getBuildSql($dbName = 'oauth2_server_php')
	{
		$sql = "
        CREATE TABLE {$this->config['job_table']} (
          client_id             VARCHAR(80)   NOT NULL,
          client_secret         VARCHAR(80),
          redirect_uri          VARCHAR(2000),
          grant_types           VARCHAR(80),
          scope                 VARCHAR(4000),
          user_id               VARCHAR(80),
          PRIMARY KEY (client_id)
        );
            CREATE TABLE {$this->config['access_token_table']} (
              access_token         VARCHAR(40)    NOT NULL,
              client_id            VARCHAR(80)    NOT NULL,
              user_id              VARCHAR(80),
              expires              TIMESTAMP      NOT NULL,
              scope                VARCHAR(4000),
              PRIMARY KEY (access_token)
            );
            CREATE TABLE {$this->config['code_table']} (
              authorization_code  VARCHAR(40)    NOT NULL,
              client_id           VARCHAR(80)    NOT NULL,
              user_id             VARCHAR(80),
              redirect_uri        VARCHAR(2000),
              expires             TIMESTAMP      NOT NULL,
              scope               VARCHAR(4000),
              id_token            VARCHAR(1000),
              PRIMARY KEY (authorization_code)
            );
            CREATE TABLE {$this->config['refresh_token_table']} (
              refresh_token       VARCHAR(40)    NOT NULL,
              client_id           VARCHAR(80)    NOT NULL,
              user_id             VARCHAR(80),
              expires             TIMESTAMP      NOT NULL,
              scope               VARCHAR(4000),
              PRIMARY KEY (refresh_token)
            );
            CREATE TABLE {$this->config['user_table']} (
              username            VARCHAR(80),
              password            VARCHAR(80),
              first_name          VARCHAR(80),
              last_name           VARCHAR(80),
              email               VARCHAR(80),
              email_verified      BOOLEAN,
              scope               VARCHAR(4000)
            );
            CREATE TABLE {$this->config['scope_table']} (
              scope               VARCHAR(80)  NOT NULL,
              is_default          BOOLEAN,
              PRIMARY KEY (scope)
            );
            CREATE TABLE {$this->config['jwt_table']} (
              client_id           VARCHAR(80)   NOT NULL,
              subject             VARCHAR(80),
              public_key          VARCHAR(2000) NOT NULL
            );
            CREATE TABLE {$this->config['jti_table']} (
              issuer              VARCHAR(80)   NOT NULL,
              subject             VARCHAR(80),
              audiance            VARCHAR(80),
              expires             TIMESTAMP     NOT NULL,
              jti                 VARCHAR(2000) NOT NULL
            );
            CREATE TABLE {$this->config['public_key_table']} (
              client_id            VARCHAR(80),
              public_key           VARCHAR(2000),
              private_key          VARCHAR(2000),
              encryption_algorithm VARCHAR(100) DEFAULT 'RS256'
            )
        ";

		return $sql;
	}
}
