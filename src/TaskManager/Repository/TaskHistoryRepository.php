<?php

namespace Mepatek\TaskManager\Repository;

use Mepatek\TaskManager\Mapper\IMapper,
	Mepatek\TaskManager\Entity\TaskHistory,
	Mepatek\TaskManager\Entity\Task;

class TaskHistoryRepository extends AbstractRepository
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
	 * @param TaskHistory $item
	 * @return boolean
	 */
	public function save(TaskHistory &$item)
	{
		return $this->mapper->save($item);
	}


	/**
	 * Delete task history
	 *
	 * @param integer $id
	 * @return boolean
	 */
	public function delete($id)
	{
		return $this->mapper->delete($id);
	}

	/**
	 * Find by id
	 *
	 * @param integer $id
	 * @return TaskHistory
	 */
	public function find($id)
	{
		return $this->mapper->find((int)$id);
	}

	/**
	 * Find first item by $values (key=>value)
	 * @param array $values
	 * @param array $order Order => column=>ASC/DESC
	 * @return TaskHistory
	 */
	public function findOneBy(array $values, $order=null)
	{
		return $this->mapper->findOneBy($values, $order);
	}


	/**
	 * Find history for taskId
	 * @param $taskId
	 * @return TaskHistory[]
	 */
	public function findByTaskId( $taskId )
	{
		$values = array(
			"taskId" => $taskId,
		);
		$order = array(
			"started" => "DESC",
		);
		return $this->mapper->findBy( $values, $order );
	}

	/**
	 * Fill task with history array
	 * @return \Mepatek\TaskManager\Entity\TaskHistory[]
	 */
	public function fillTask( Task $task )
	{
		return ( $task->history = $this->findByTaskId( $task->id ) );
	}

	/**
	 * Fill array of task with history array
	 * @param Task[] $tasks
	 * @return bool
	 */
	public function fillTasks( array $tasks )
	{
		$success = true;
		foreach ( $tasks as $task ) {
			$success = $this->fillTask( $task )
				and $success;
		}
		return $success;
	}




}
