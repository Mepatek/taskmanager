<?php

namespace Mepatek\TaskManager\Mapper;

use Mepatek\Mapper\IMapper;
use Mepatek\Mapper\AbstractNetteDatabaseMapper;
use Mepatek\Logger;

use Nette,
	Nette\Database\Context,
	Mepatek\TaskManager\Entity,
	Mepatek\TaskManager\Entity\TaskHistory;


/**
 * Class TaskHistoryNetteDatabaseMapper
 * @package Mepatek\TaskManager\Mapper
 */
class TaskHistoryNetteDatabaseMapper extends AbstractNetteDatabaseMapper implements IMapper
{
	/**
	 * TaskHistoryNetteDatabaseMapper constructor.
	 *
	 * @param Context     $database
	 * @param Logger|null $logger
	 */
	public function __construct(Context $database, Logger $logger = null)
	{
		$this->database = $database;
		$this->logger = $logger;
	}

	/**
	 * Save item
	 *
	 * @param TaskHistory $item
	 *
	 * @return boolean
	 */
	public function save(&$item)
	{
		$data = $this->itemToData($item);
		$retSave = false;

		if (!$item->id) { // new --> insert

			unset($data["TaskHistoryID"]);

			$row = $this->getTable()
				->insert($data);
			if ($row) {
				$item->id = $row["TaskHistoryID"];
				$this->logInsert(__CLASS__, $item);
				$retSave = true;
			}
		} else { // update
			$item_old = $this->find($item->id);
			unset($data["TaskHistoryID"]);

			$row = $this->getTable()
				->where("TaskHistoryID", $item->id)
				->update($data);
			if ($row) {
				$this->logSave(__CLASS__, $item_old, $item);
				$retSave = true;
			}
		}

		return $retSave;
	}

	/**
	 * Item data to array
	 *
	 * @param TaskHistory $item
	 *
	 * @return array
	 */
	private function itemToData(TaskHistory $item)
	{
		$data = [];

		foreach ($this->mapItemPropertySQLNames() as $property => $columnSql) {
			$data[$columnSql] = $item->$property;
		}

		return $data;
	}

	/**
	 * Get array map of item property vs SQL columns name for TaskHistory table
	 * @return array
	 */
	protected function mapItemPropertySQLNames()
	{
		return [
			"id"         => "TaskHistoryID",
			"taskId"     => "TaskID",
			"started"    => "Started",
			"finished"   => "Finished",
			"resultCode" => "ResultCode",
			"user"       => "User",
			"output"     => "Output",
		];
	}

	/**
	 * Get view object
	 * @return \Nette\Database\Table\Selection
	 */
	protected function getTable()
	{
		$table = $this->database->table("TaskHistory");
		return $table;
	}

	/**
	 * Find 1 entity by ID
	 *
	 * @param string $id
	 *
	 * @return TaskHistory
	 */
	public function find($id)
	{
		$values["id"] = $id;
		$item = $this->findOneBy($values);
		return $item;
	}

	/**
	 * Find first entity by $values (key=>value)
	 *
	 * @param array $values
	 * @param array $order Order => column=>ASC/DESC
	 *
	 * @return TaskHistory
	 */
	public function findOneBy(array $values, $order = null)
	{
		$items = $this->findBy($values, $order, 1);
		if (count($items) > 0) {
			return $items[0];
		} else {
			return null;
		}
	}

	/**
	 * Delete item
	 *
	 * @param integer $id
	 *
	 * @return boolean
	 */
	public function delete($id)
	{
		$deletedRow = 0;
		if (($item = $this->find($id))) {

			$deletedRow = $this->getTable()
				->where("TaskHistoryID", $id)
				->delete();

			$this->logDelete(__CLASS__, $item, "DEELETE WHERE TaskHistoryID=" . $id . " (cnt: $deletedRow)");
		}
		return $deletedRow > 0;
	}

	/**
	 * Delete all history record older than x days
	 *
	 * @param integer $maxDays
	 *
	 * @return integer count of deleted record
	 */
	public function deleteOlderThanDays($maxDays)
	{
		$values =
			[
				"started < DATE_SUB(CURDATE(),INTERVAL ? DAY)" => $maxDays,
			];
		$deleteCount = $this->selectionBy($values)
			->delete();
		return $deleteCount;
	}

	/**
	 * Delete all history record over x count
	 *
	 * @param integer $maxCount
	 *
	 * @return integer count of deleted record
	 */
	public function deleteOverCount($maxCount)
	{
		$selection = $this->selectionBy([], ["id" => "DESC"], PHP_INT_MAX, $maxCount);
		$values = [
			"TaskHistoryID" => $selection,
		];
		$deleteCount = $this->selectionBy($values)
			->delete();
		return $deleteCount;
	}

	/**
	 * from data to item
	 *
	 * @param \Nette\Database\IRow $data
	 *
	 * @return TaskHistory
	 */
	protected function dataToItem($data)
	{
		$item = new TaskHistory;

		foreach ($this->mapItemPropertySQLNames() as $property => $columnSql) {
			$item->$property = $data->$columnSql;
		}

		return $item;
	}
}
