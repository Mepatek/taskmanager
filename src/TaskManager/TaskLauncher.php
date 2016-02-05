<?php

namespace Mepatek\TaskManager;


use Nette\Database\Context,
	Mepatek\TaskManager\Repository\TaskRepository,
	Mepatek\TaskManager\Mapper\TaskNetteDatabaseMapper,
	Mepatek\TaskManager\Entity\Task;


class TaskLauncher
{

	/** @var Context */
	private $database;
	/** @var \Nette\DI\Container */
	private $container;
	/** @var string */
	private $taskskDir;

	/** @var TaskRepository */
	private $taskRepository;

	/**
	 * TaskLauncher constructor.
	 * @param Context $database
	 * @param string $container
	 * @param string $tasksDir
	 */
	public function __construct( $database, $container, $tasksDir )
	{
		$this->database = $database;
		$this->container = $container;
		$this->tasksDir = $tasksDir;
		$taskMapper = new TaskNetteDatabaseMapper( $this->database );
		$this->taskRepository = new TaskRepository( $taskMapper );
	}

	/**
	 * Run all tasks in plan
	 */
	public function run( )
	{

		/*
		$task = new \Mepatek\TaskManager\Entity\Task();
		$task->name = "Načítání přijatých dokladů";
		$task->source = "admin";
		$task->author = "Mepatek";
		$task->description = "Pravidelně prochází adresář se skeny a načítá dokumenty do Varia k přijatým dokladům";
		$this->taskRepository->save($task);
		*/

		$task = new Task(  );
		// find all the tasks to be run
		$tasks = $this->taskRepository->findTasksToRun();
		foreach ($tasks as $task) {

			// if set state running run task
			if ( $this->taskRepository->setStateRunning($task) ) {

				$success = $task->run( $this->container, $this->taskskDir );

				$this->taskRepository->save($task);
			}
		}
		var_dump($tasks);
		echo "XXX";
	}
}