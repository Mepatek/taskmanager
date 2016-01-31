<?php

namespace Mepatek\TaskManager\Mapper;

use Nette,
	Nette\Database\Context,
	Mepatek\TaskManager\Entity\Task;

/**
 * Mapper NetteDatabase for Documents
 *
 * Document is saved in table DOLEXXXX.dbo.Dokumenty
 *
 */
class TaskNetteDatabaseMapper extends AbstractNetteDatabaseMapper implements IMapper
{
	/** Nette\Database\Context */
	private $databaseDOLE;
	/** App\Model\Vario */
	private $vario;

	/**
	 * DokumentNetteDatabaseMapper constructor.
	 * @param Context $databaseDOLE
	 * @param Vario $vario
	 * @param Logger|null $logger
	 */
	public function __construct(Context $databaseDOLE, Vario $vario, Logger $logger=null)
	{
		$this->databaseDOLE = $databaseDOLE;
		$this->vario = $vario;
		$this->logger = $logger;
	}

	/**
	 * Save zakazka
	 */
	public function save(&$item)
	{
		if (! $item->id) { // new --> insert
			// must be set correct!

			$data = $this->itemToData($item);
			//$data["rowguid"] = new Nette\Database\SqlLiteral("UUID()");
			$data["Datum_aktualizace"] = new Nette\Utils\DateTime();
			$data["Format_ulozeni"] = 0;
			if ( ! $data["Kniha"] ) {
				$data["Kniha"] = "Dokumenty" . ($data["Agenda"] ? " - " . $data["Agenda"] : "");
			}

			// set primary ID
			$data["ID"] = $this->getID($item);

			$this->getTable()
				->insert($data);
			$newItem = $this->find($data["ID"]);
			if ($newItem) {
				$this->logInsert(__CLASS__, $newItem);
				$item = $newItem;
				return true;
			} else {
				return false;
			}

		} else { // update
			$data = $this->itemToData($item);
			unset($data["ID"]);
			unset($data["Zaznam"]);
			unset($data["Agenda"]);
			unset($data["Kniha"]);
			$oldItem = $this->find($item->id);
			$this->getTable()
				->where("ID", $item->id)
				->update($data);
			$this->logSave(__CLASS__, $oldItem, $item);
			return true;
		}

		return true;
	}

	/**
	 * Get ID for new item
	 * @param Dokument $item
	 */
	private function getID(Dokument $item)
	{
		// id is string 30:
		// 16 - uid + max 14 autor (mezery=>_)
		$id = uniqid() . "001" . substr($item->autor, 0, 14);
		//TODO: test if id not exist
		return $id;
	}

	/**
	 * Delete dokument
	 * @param string $id
	 * @return boolean
	 */
	public function delete($id)
	{
		$deletedRow = 0;
		if (($item = $this->find($id))) {
			$deletedRow = $this->getTable()
				->where("ID", $id)
				->delete();
			$this->logDelete(__CLASS__, $item, "Delete * from Dokumenty Where ID=" . $id . " (cnt: $deletedRow)");
		}
		return $deletedRow > 0;
	}

	/**
	 * Find 1 entity by ID
	 *
	 * @param string $id
	 * @return \App\Model\Entity\Dokument
	 */
	public function find($id)
	{
		$values["id"] = $id;
		return $this->findOneBy($values);
	}

	/**
	* Find first entity by $values (key=>value)
	* @param array $values
	* @param array $order Order => column=>ASC/DESC
	* @return \App\Model\Entity\Dokument
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
	* Get view object
	* @return \Nette\Database\Table\Selection
	*/
	protected function getTable()
	{
		return $this->databaseDOLE->table("Dokumenty");
	}

	/**
	 * Item data to array
	 *
	 * @param \App\Model\Entity\Dokument $item
	 * @return array
	 */
	private function itemToData(Dokument $item)
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
	 * @return \App\Model\Entity\Dokument
	 */
	protected function dataToItem($data)
	{
		$item = new Dokument;

		foreach ($this->mapItemPropertySQLNames() as $property => $columnSql) {
			$item->$property = $data->$columnSql;
		}

		return $item;
	}


	/**
	 * Get array map of item property vs SQL columns name for ZAK_Zakazky table
	 * @return array
	 */
	protected function mapItemPropertySQLNames()
	{
		return array (
			"id"			=> "ID",
			"zaznam"		=> "Zaznam",
			"agenda"		=> "Agenda",
			"kniha"			=> "Kniha",
			"predmet"		=> "Predmet",
			"typ"			=> "Typ_dokumentu",
			"autor"			=> "Autor",
			"datumCas"		=> "Datum_a_cas",
			"popis"			=> "Popis",
			"kategorie"		=> "Kategorie_dokumentu",
			"urlSouboru"	=> "Soubor",
		);
	}

}
