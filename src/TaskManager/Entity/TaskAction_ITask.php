<?php


namespace Mepatek\TaskManager\Entity;



/*
 * Data JSON:
 * ClassName (without namespace)
 * Namespace
 * Arguments[]
 */
use Nette\Neon\Exception;

/**
 * Class TaskAction_ITask
 * @package Mepatek\TaskManager\Entity
 */
class TaskAction_ITask extends TaskAction
{

	/** @var string */
	protected $className;
	/** @var string */
	protected $nameSpace = "";
	/** @var array */
	protected $arguments = array();

	/**
	 * @return string
	 */
	public function getType()
	{
		return "ITask";
	}

	/**
	 * @return string
	 */
	public function getClassName()
	{
		return $this->className;
	}

	/**
	 * @param string $className
	 */
	public function setClassName($className)
	{
		$this->className = $className;
	}

	/**
	 * @return string
	 */
	public function getNameSpace()
	{
		return $this->nameSpace;
	}

	/**
	 * @param string $nameSpace
	 */
	public function setNameSpace($nameSpace)
	{
		$this->nameSpace = $nameSpace;
	}

	/**
	 * @return array
	 */
	public function getArguments()
	{
		return $this->arguments;
	}

	/**
	 * @param array $arguments
	 */
	public function setArguments($arguments)
	{
		$this->arguments = $arguments;
	}


	/**
	 * @return string
	 */
	public function getData()
	{
		$data = json_encode(
			array (
				"ClassName" => $this->className,
				"NameSpace" => $this->nameSpace,
				"Arguments" => $this->arguments,
			)
		);
		return $data;
	}

	/**
	 * @param mixed $data
	 */
	public function setData($data)
	{
		$decodedData = json_decode( $data );

		if ($decodedData) {
			$this->className = $decodedData->ClassName;
			$this->nameSpace = $decodedData->NameSpace;
			$this->arguments = $decodedData->Arguments ? $decodedData->Arguments : array();
		}

	}



	/**
	 * Run task implements from ITask
	 * @param \Nette\DI\Container $container
	 * @param string $tasksDir
	 * @return bool
	 * @throws \Exception
	 */
	public function run( $container, $tasksDir )
	{
		$success = false;

		$fileTask =  $tasksDir . DIRECTORY_SEPARATOR . $this->className . ".php";
		$class = $this->nameSpace . "\\" . $this->className;

		if ( file_exists( $fileTask ) ) {
			require_once( $fileTask );

			$itask = new $class( $container, $this->arguments );
			if ( ! $itask instanceof \Mepatek\TaskManager\ITask ) {
				// TODO: exception
				throw new \Exception('Třída "' . $class . '" nemá implementováno ITask');
			} else {
				$success = $itask->run();
			}

		} else {
			// TODO: exceptions
			throw new \Exception('Neexistuje soubor "' . $fileTask . '" s třídou "' . $class . '"');
			$success = false;
		}


		return $success;

	}


}
