<?php

namespace Mepatek\TaskManager;

use Exception;

use Nette\Database\Context,
	Mepatek\TaskManager\Entity\Output,
	Mepatek\TaskManager\Repository\TaskRepository,
	Mepatek\TaskManager\Mapper\TaskNetteDatabaseMapper,
	Mepatek\TaskManager\Repository\TaskHistoryRepository,
	Mepatek\TaskManager\Mapper\TaskHistoryNetteDatabaseMapper,
	Mepatek\TaskManager\Entity\TaskHistory,
	Mepatek\TaskManager\Entity\Task,
	Nette\Utils\DateTime;


class TaskLauncher
{

	/** @var Context */
	private $database;
	/** @var \Nette\DI\Container */
	private $container;
	/** @var string */
	private $tasksDir;

	/** @var TaskRepository */
	private $taskRepository;
	/** @var TaskHistoryRepository */
	private $taskHistoryRepository;


	/** @var int maximum days for history */
	private $maxHistoryDays = 30;
	/** @var int maximum count of record for history */
	private $maxHistoryRecords = 10000;

	/**
	 * TaskLauncher constructor.
	 *
	 * @param Context $database
	 * @param string  $container
	 * @param string  $tasksDir
	 */
	public function __construct($database, $container, $tasksDir)
	{
		$this->database = $database;
		$this->container = $container;
		$this->tasksDir = $tasksDir;

		$taskMapper = new TaskNetteDatabaseMapper($this->database);
		$this->taskRepository = new TaskRepository($taskMapper);
		$taskHistoryMapper = new TaskHistoryNetteDatabaseMapper($this->database);
		$this->taskHistoryRepository = new TaskHistoryRepository($taskHistoryMapper);
	}

	/**
	 * Run task (taskName) withou planing from console
	 *
	 * @param $taskName
	 *
	 * @return bool
	 */
	public function consoleRun($taskName)
	{
		$this->setUpFromConfig();
		$this->cleanUp();

		$task = $this->taskRepository->findOneBy(["name" => $taskName]);
		if ($task) {
			return $this->runTask($task);
		} else {
			return false;
		}
	}

	/**
	 * Run all tasks in plan
	 */
	public function run()
	{

		$this->setUpFromConfig();
		$this->cleanUp();

		$success = true;

		// find all the tasks to be run
		$tasks = $this->taskRepository->findTasksToRun();
		foreach ($tasks as $task) {

			$success = $this->runTask($task) and $success;

		}

		return $success;
	}

	/**
	 * Run clean up tasks:
	 * kill exceeded tasks
	 * clear history
	 */
	protected function cleanUp()
	{
		// clean
		$this->killExceededTasks();
		$this->cleanHistory();
	}

	protected function setUpFromConfig()
	{
		// set default time limit 4 hour
		$timeLimit = 4 * 60 * 60;

		$parameters = $this->container->getParameters();
		if (is_array($parameters) and array_key_exists("taskManager", $parameters)) {
			/// ***** TIMELIMIT ****** //
			if (isset($parameters["taskManager"]["timeLimit"])) {
				$timeLimit = $parameters["taskManager"]["timeLimit"];
			}
			/// ***** MAX HISTORY DAYS ****** //
			if (isset($parameters["taskManager"]["history"]["maxDays"])) {
				$this->maxHistoryDays = (int)$parameters["taskManager"]["history"]["maxDays"];
			}
			/// ***** MAX HISTORY RECORDS ****** //
			if (isset($parameters["taskManager"]["history"]["maxRecords"])) {
				$this->maxHistoryRecords = (int)$parameters["taskManager"]["history"]["maxRecords"];
			}
		}

		set_time_limit($timeLimit);
	}

	/**
	 * Run one task ...
	 *
	 * @param Task $task
	 *
	 * @return bool
	 */
	protected function runTask(Task $task)
	{
		// task history ..
		$taskHistory = new TaskHistory();
		$taskHistory->taskId = $task->id;
		$taskHistory->started = new DateTime();
		$taskHistory->output = new Output;
		$this->taskHistoryRepository->save($taskHistory);

		$resultCode = 0;

		// if set state running run task
		try {
			$this->taskRepository->setStateRunning($task);

			$success = $task->run($this->container, $this->tasksDir, $taskHistory->output);

		} catch (Exception $e) {
			$resultCode = $e->getCode();
			$taskHistory->output->error(
				"(code: " . $e->getCode() . ") " . $e->getMessage() . "\n"
				. "file '" . $e->getFile() . "' (line " . $e->getLine() . ")\n"
				. $e->getTraceAsString() . "\n\n"
			);
			$success = false;
		}


		$taskHistory->finished = new DateTime();
		$taskHistory->resultCode = $resultCode;
		$this->taskHistoryRepository->save($taskHistory);

		// set state idle
		$this->setTaskIdleAndSave($task);
		return $success;
	}

	/**
	 * Set state to 0 for task with expiration over MaxExecutionTimeInSecond
	 * Set lastSuccess to false
	 * Add info to history
	 */
	protected function killExceededTasks()
	{
		$exceededTasks = $this->taskRepository->findExceededTasks();
		foreach ($exceededTasks as $task) {
			$task->lastSuccess = false;
			$this->setTaskIdleAndSave($task);
			// task history ..
			$taskHistory = new TaskHistory();
			$taskHistory->taskId = $task->id;
			$taskHistory->started = new DateTime();
			$taskHistory->finished = new DateTime();
			$taskHistory->output = new Output;
			$taskHistory->output->error("Exceeded time to run");
			$taskHistory->resultCode = -1;
			$this->taskHistoryRepository->save($taskHistory);
		}
	}

	/**
	 * clear all records older than x days and all over the max count
	*/
	protected function cleanHistory()
	{
		$this->taskHistoryRepository->deleteOlderThanDays($this->maxHistoryDays);
		$this->taskHistoryRepository->deleteOverCount($this->maxHistoryRecords);
	}

	/**
	 * Set state to idle
	 * set last and next run
	 *
	 * @param Task $task
	 */
	protected function setTaskIdleAndSave(Task $task)
	{
		// set state idle
		$task->setLastAndNextRun();
		$task->exceedDateTime = null;
		$task->state = 0;
		$this->taskRepository->save($task);

	}
}
