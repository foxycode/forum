<?php

namespace App\Model;

use Nette;

abstract class Repository
{
    use Nette\SmartObject;

    /**
     * @var Nette\Database\Context
     */
    protected $database;

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
     * @return Nette\Database\Table\Selection
     */
    public function findAll()
    {
        return $this->getTable();
    }

    /**
     * @return Nette\Database\Table\Selection
     */
    public function findBy(array $by)
    {
        return $this->getTable()->where($by);
    }

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
