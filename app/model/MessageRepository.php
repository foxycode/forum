<?php declare(strict_types=1);

namespace App\Model;

use Nette\Database\ResultSet;

final class MessageRepository extends Repository
{
    public function getByThreadId(int $id): ResultSet
    {
        $result = $this->database->query("
            SELECT *
            FROM message
            JOIN user ON user.user_id = message.submiter_id
            WHERE thread_id = {$id}
            ORDER BY message_id
        ");

        return $result;
    }
}
