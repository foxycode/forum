<?php declare(strict_types=1);

namespace App\Model;

use Nette;
use Nette\Database\Context;

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
