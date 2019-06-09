<?php declare(strict_types=1);

namespace App\Model;

use Nette;
use Nette\Database\Context;
use Nette\Database\Table\Selection;

abstract class Repository
{
    use Nette\SmartObject;

    /**
     * @var Context
     */
    protected $database;

    public function __construct(Context $database)
    {
        $this->database = $database;
    }

    public function uncamelize(string $camel, string $splitter = '_'): string
    {
        return strtolower(preg_replace(
            '/(?!^)[[:upper:]][[:lower:]]/',
            '$0',
            preg_replace('/(?!^)[[:upper:]]+/', $splitter . '$0', $camel)
        ));
    }

    protected function getTable(): Selection
    {
        // název tabulky odvodíme z názvu třídy
        preg_match('#(\w+)Repository$#', get_class($this), $m);
        return $this->database->table($this->uncamelize($m[1]));
    }

    public function getCount(): int
    {
        return $this->getTable()->count('*');
    }

    public function findAll(): Selection
    {
        return $this->getTable();
    }

    public function findBy(array $by): Selection
    {
        return $this->getTable()->where($by);
    }

    public function insert(array $values): string
    {
        $this->getTable()->insert($values);
        return $this->database->getInsertId();
    }

    public function begin(): void
    {
        $this->database->beginTransaction();
    }

    public function rollback(): void
    {
        $this->database->rollback();
    }

    public function commit(): void
    {
        $this->database->commit();
    }
}
