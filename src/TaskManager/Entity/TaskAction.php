<?php


namespace Mepatek\TaskManager\Entity;


/**
 * Class TaskAction
 * @package Mepatek\TaskManager\Entity
 */
abstract class TaskAction extends AbstractEntity implements ITaskAction
{

	/** @var integer */
	protected $id = NULL;
	/** @var integer */
	protected $order;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 */
	public function setId($id)
	{
		// ONLY if id is not set
		if ( ! $this->id ) {
			$this->id = (int)$id;
		}
	}


	/**
	 * @return int
	 */
	public function getOrder()
	{
		return $this->order;
	}

	/**
	 * @param int $order
	 */
	public function setOrder($order)
	{
		$this->order = (int)$order;
	}


}
