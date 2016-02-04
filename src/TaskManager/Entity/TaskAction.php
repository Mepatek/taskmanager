<?php


namespace Mepatek\TaskManager\Entity;


/**
 * Class TaskAction
 * @package Mepatek\TaskManager\Entity
 */
abstract class TaskAction extends AbstractEntity
{

	/** @var integer */
	protected $id = NULL;
	/** @var string 30 */
	protected $type;
	/** @var mixed save as JSON string */
	protected $data;
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
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param string $type
	 */
	public function setType($type)
	{
		$this->type = $this->StringTruncate($type, 30);
	}

	/**
	 * @return mixed
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * @param mixed $data
	 */
	public function setData($data)
	{
		$this->data = $data;
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
