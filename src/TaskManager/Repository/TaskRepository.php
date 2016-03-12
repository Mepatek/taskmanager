<?php

namespace Mepatek\TaskManager\Repository;

use Mepatek\Mapper\IMapper,
	Mepatek\Repository\AbstractRepository;

use Mepatek\TaskManager\Entity\Task;

/**
 * Class TaskRepository
 * @package Mepatek\TaskManager\Repository
 */
class TaskRepository extends AbstractRepository
{

	/**
	 * Constructor
	 *
	 * @param IMapper $mapper
	 */
	public function __construct(IMapper $mapper)
	{
		$this->mapper = $mapper;
	}

	/**
	 * Save
	 *
	 * @param Task $item
	 *
	 * @return boolean
	 */
	public function save(Task &$item)
	{
		return $this->mapper->save($item);
	}


	/**
	 * Permanently delete task
	 *
	 * @param integer $id
	 *
	 * @return boolean
	 */
	public function deletePermanently($id)
	{
		return $this->mapper->deletePermanently($id);
	}

	/**
	 * Find by id
	 *
	 * @param integer $id
	 *
	 * @return Task
	 */
	public function find($id)
	{
		return $this->mapper->find((int)$id);
	}

	/**
	 * Find first item by $values (key=>value)
	 *
	 * @param array $values
	 * @param array $order Order => column=>ASC/DESC
	 *
	 * @return Task
	 */
	public function findOneBy(array $values, $order = null)
	{
		return $this->mapper->findOneBy($values, $order);
	}


	/**
	 * Find all task to run now
	 * Where:
	 * NextRun = NULL OR NextRun<=NOW()
	 * Disabled = FALSE
	 * Deleted = FALSE
	 * State = 0
	 */
	public function findTasksToRun()
	{
		return $this->mapper->findTasksToRun();
	}


	/**
	 * Set task state to running (State = 1)
	 *
	 * @param Task $task
	 *
	 * @return bool
	 */
	public function setStateRunning(Task $task)
	{
		return $this->mapper->setStateRunning($task);
	}

}
