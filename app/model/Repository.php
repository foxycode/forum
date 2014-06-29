<?php

namespace App\Model;

use Nette;


/**
 * Base repository.
 */
abstract class Repository extends Nette\Object
{
	/** @var Nette\Database\Context */
	protected $database;

	// -------------------------------------------------------------------------

	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}

	function uncamelize($camel, $splitter = "_")
	{
		$camel = preg_replace('/(?!^)[[:upper:]][[:lower:]]/', '$0', preg_replace('/(?!^)[[:upper:]]+/', $splitter.'$0', $camel));
		return strtolower($camel);
	}

	/**
	 * Vrací objekt reprezentující databázovou tabulku.
	 * @return Nette\Database\Table\Selection
	 */
	protected function getTable()
	{
		// název tabulky odvodíme z názvu třídy
		preg_match('#(\w+)Repository$#', get_class($this), $m);
		return $this->database->table($this->uncamelize($m[1]));
	}

	public function getCount()
	{
		return $this->getTable()->count('*');
	}

	/**
	 * Vrací všechny řádky z tabulky.
	 * @return Nette\Database\Table\Selection
	 */
	public function findAll()
	{
		return $this->getTable();
	}

	/**
	 * Vrací řádky podle filtru, např. array('name' => 'John').
	 * @return Nette\Database\Table\Selection
	 */
	public function findBy(array $by)
	{
		return $this->getTable()->where($by);
	}

	/**
	 * Vrací záznam podle primárního klíče
	 * @return Nette\Database\Table\Row
	 */
	// public function get($id)
	// {
	// 	return $this->getTable()->find($id)->fetch();
	// }

	public function insert($values)
	{
		$this->getTable()->insert($values);
		return $this->database->getInsertId();
	}

	public function update($id, $values)
	{
		$this->get($id)->update($values);
	}

	public function begin()
	{
		$this->database->beginTransaction();
	}

	public function rollback()
	{
		$this->database->rollback();
	}
	
	public function commit()
	{
		$this->database->commit();
	}

}
