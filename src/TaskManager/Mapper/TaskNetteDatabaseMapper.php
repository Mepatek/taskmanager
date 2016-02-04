<?php

namespace Mepatek\TaskManager\Mapper;

use Nette,
	Nette\Database\Context,
	Mepatek\TaskManager\Entity\Task,
	Mepatek\TaskManager\Entity\TaskAction,
	Mepatek\TaskManager\Entity\TaskCondition;


/**
 * Class TaskNetteDatabaseMapper
 * @package Mepatek\TaskManager\Mapper
 */
class TaskNetteDatabaseMapper extends AbstractNetteDatabaseMapper implements IMapper
{
	/** @var Nette\Database\Context */
	private $database;

	/** @var boolean TRUE - find deleted row */
	private $deleted;

	/**
	 * TaskNetteDatabaseMapper constructor.
	 * @param Context $database
	 * @param Logger|null $logger
	 */
	public function __construct(Context $database, Logger $logger=null)
	{
		$this->database = $database;
		$this->logger = $logger;
	}

	/**
	 * Save item
	 * @param Task $item
	 * @return boolean
	 */
	public function save(&$item)
	{
		$data = $this->itemToData($item);

		if (! $item->id) { // new --> insert

			unset($data["TaskID"]);
			$data["Created"] = new Nette\Utils\DateTime();

			$row = $this->getTable()
				->insert($data);
			if ($row) {
				$item->id = $row["TaskID"];
				$this->logInsert(__CLASS__, $item);
				return true;
			} else {
				return false;
			}
		} else { // update
			$item_old = $this->find($item->id);
			unset($data["TaskID"]);
			unset($data["Created"]);

			$row = $this->getTable()
				->where("TaskID", $item->id)
				->update($data);
			if ($row) {
				$item = $this->find($item->id);
				$this->logSave(__CLASS__, $item_old, $item);
				return true;
			} else {
				return false;
			}
		}

		$this->saveActions($item);
		$this->saveConditions($item);
		return true;
	}

	/**
	 * Delete item
	 * @param integer $id
	 * @return boolean
	 */
	public function delete($id)
	{
		$deletedRow = 0;
		if (($item = $this->find($id))) {

			$deleted = $this->deleted;
			$this->deleted = true;

			$deletedRow = $this->getTable()
				->where("TaskID", $id)
				->update(
					array(
						"Deleted" => TRUE,
					)
				);

			$this->deleted = $deleted;

			$this->logDelete(__CLASS__, $item, "UPDATE SET Deleted WHERE TaskID=" . $id . " (cnt: $deletedRow)");
		}
		return $deletedRow > 0;
	}

	/**
	 * Permanently delete item
	 * @param integer $id
	 * @return boolean
	 */
	public function deletePermanently($id)
	{
		$deletedRow = 0;
		if (($item = $this->find($id))) {

			$deleted = $this->deleted;
			$this->deleted = true;

			$deletedRow = $this->getTable()
				->where("TaskID", $id)
				->delete();

			$this->deleted = $deleted;

			$this->logDelete(__CLASS__, $item, "DELETE FROM Tasks WHERE TaskID=" . $id . " (cnt: $deletedRow)");
		}
		return $deletedRow > 0;
	}

	/**
	 * Find 1 entity by ID
	 *
	 * @param string $id
	 * @return Task
	 */
	public function find($id)
	{
		$values["id"] = $id;
		$deleted = $this->deleted;
		$this->deleted = true;

		$item = $this->findOneBy($values);

		$this->deleted = $deleted;
		return $item;
	}

	/**
	* Find first entity by $values (key=>value)
	* @param array $values
	* @param array $order Order => column=>ASC/DESC
	* @return Task
	*/
	public function findOneBy(array $values, $order=null)
	{
		$items = $this->findBy($values, $order, 1);
		if (count($items)>0) {
			return $items[0];
		} else {
			return NULL;
		}
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
		$now = new \Nette\Utils\DateTime();
		return $this->findBy(
			array(
				"(NextRun IS NULL OR NextRun<=?)" => $now,
				"disabled" => false,
				"deleted" => false,
				"state" => 0,
			)
		);
	}

	/**
	* Get view object
	* @return \Nette\Database\Table\Selection
	*/
	protected function getTable()
	{
		$table = $this->database->table("Tasks");
		if ( ! $this->deleted ) {
			$table->where("Deleted",FALSE);
		}
		return $table;
	}

	/**
	 * Item data to array
	 *
	 * @param Task $item
	 * @return array
	 */
	private function itemToData(Task $item)
	{
		$data = array();

		foreach ($this->mapItemPropertySQLNames() as $property => $columnSql) {
			$data[$columnSql] = $item->$property;
		}

		return $data;
	}

	/**
	 * from data to item
	 *
	 * @param \Nette\Database\IRow $data
	 * @return Task
	 */
	protected function dataToItem($data)
	{
		$item = new Task;

		foreach ($this->mapItemPropertySQLNames() as $property => $columnSql) {
			$item->$property = $data->$columnSql;
		}

		$this->loadActions($item);
		$this->loadConditions($item);
		return $item;
	}


	/**
	 * load actions to item
	 *
	 * @param Task $item
	 */
	private function loadActions(&$item)
	{
		$item->deleteAllActions();
		$actions = $this->database
			->table("TaskActions")
			->where("TaskID", $item->id);
		foreach ($actions as $action_data) {
			$class = "TaskAction_" . $action_data->Type;
			$action = new $class;

			$action->id = $action_data->TaskActionID;
			$action->type = $action_data->Type;
			$action->data = $action_data->Data;
			$action->order = $action_data->Order;

			$item->addAction($action);

		}
	}

	/**
	 * Save actions from item
	 *
	 * @param Task $item
	 */
	public function saveActions($item)
	{
		$ids = array();
		foreach ($item->actions as $action) {
			$exists = $this->database
				->table("TaskActions")
				->where("TaskActionID", $action->id)
				->count() > 0;

			$data = array(
				"TaskID"	=> $item->id,
				"Type"		=> $action->type,
				"Data"		=> $action->data,
				"Order"		=> $action->order,
			);

			if ($exists) {
				$this->database
					->table("TaskActions")
					->where("TaskActionID", $action->id)
					->update($data);
			} else {
				$row = $this->database
					->table("TaskActions")
					->insert($data);
				$action->id = $row["TaskActionID"];
			}
			$ids = $action->id;
		}


		$this->database
			->table("TaskActions")
			->where("TaskActionID NOT IN ?", $ids)
			->delete();

	}

	/**
	 * load conditions to item
	 *
	 * @param Task $item
	 */
	private function loadConditions(&$item)
	{
		$item->deleteAllConditions();
		$conditions = $this->database
			->table("TaskConditions")
			->where("TaskID", $item->id);
		foreach ($conditions as $condition_data) {
			$class = "TaskCondition_" . $condition_data->Type;
			$condition = new $class;

			$condition->id = $condition_data->TaskActionID;
			$condition->type = $condition_data->Type;
			$condition->data = $condition_data->Data;
			$condition->created = $condition_data->Created;
			$condition->expired = $condition_data->Expired;
			$condition->order = $condition_data->Order;

			$item->addCondition($condition);

		}
	}

	/**
	 * Save conditions from item
	 *
	 * @param Task $item
	 */
	public function saveConditions($item)
	{
		$ids = array();
		foreach ($item->conditions as $condition) {
			$exists = $this->database
					->table("TaskConditions")
					->where("TaskConditionID", $condition->id)
					->count() > 0;

			$data = array(
				"TaskID"	=> $item->id,
				"Type"		=> $condition->type,
				"Data"		=> $condition->data,
				"Created"	=> $condition->created,
				"Expired"	=> $condition->expired,
				"Order"		=> $condition->order,
			);

			if ($exists) {
				$this->database
					->table("TaskConditions")
					->where("TaskConditionID", $condition->id)
					->update($data);
			} else {
				$row = $this->database
					->table("TaskConditions")
					->insert($data);
				$condition->id = $row["TaskConditionID"];
			}
			$ids = $condition->id;
		}


		$this->database
			->table("TaskConditions")
			->where("TaskConditionID NOT IN ?", $ids)
			->delete();

	}

	/**
	 * Get array map of item property vs SQL columns name for ZAK_Zakazky table
	 * @return array
	 */
	protected function mapItemPropertySQLNames()
	{
		return array (
			"id"			=> "TaskID",
			"name"			=> "Name",
			"created"		=> "Created",
			"source"		=> "Source",
			"author"		=> "Author",
			"description"	=> "Description",
			"deleteAfterRun"=> "DeleteAfterRun",
			"state"			=> "State",
			"disabled"		=> "Disabled",
			"nextRun"		=> "NextRun",
			"lastRun"		=> "LastRun",
		);
	}
}
