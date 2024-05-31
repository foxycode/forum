<?php declare(strict_types=1);

namespace App\Model\Repositories;

use Nette\Database\Explorer;

abstract class Repository
{
    public function __construct(
        protected readonly Explorer $database,
    ) {
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
