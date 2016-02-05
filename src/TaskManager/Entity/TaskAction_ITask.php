<?php


namespace Mepatek\TaskManager\Entity;



/*
 * Data JSON:
 * ClassName (without namespace)
 * Namespace
 * Arguments[]
 */

/**
 * Class TaskAction_ITask
 * @package Mepatek\TaskManager\Entity
 */
class TaskAction_ITask extends TaskAction
{

	/** @var string */
	private $className;
	/** @var string */
	private $nameSpace;
	/** @var array */
	private $arguments;


	/**
	 * @return string
	 */
	public function getData()
	{
		$this->data = json_encode(
			array (
				"ClassName" => $this->className,
				"NameSpace" => $this->nameSpace,
				"Arguments" => $this->arguments,
			)
		);
		return $this->data;
	}

	/**
	 * @param mixed $data
	 */
	public function setData($data)
	{
		$this->data = $data;

		$decodedData = json_decode( $data );
		$this->className = $decodedData["ClassName"];
		$this->nameSpace = $decodedData["NameSpace"];
		$this->arguments = $decodedData["Arguments"];

	}



	/**
	 * Run task implements from ITask
	 * @param \Nette\DI\Container $container
	 * @param string $tasksDir
	 * @return bool
	 */
	public function run( $container, $tasksDir )
	{
		$fileTask = $this->normalizePath( $tasksDir ) . DIRECTORY_SEPARATOR . $this->className . ".php";
		$class = $this->nameSpace . "\\" . $this->className;

		require_once( $fileTask );

		$itask = new $class;
		if ( ! $itask instanceof \Mepatek\TaskManager\ITask ) {
			// TODO: run exception
		}

		$itask->setUp( $container, $this->arguments );

		return $itask->run();

	}


}
