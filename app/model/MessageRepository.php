<?php

namespace App\Model;

class MessageRepository extends Repository
{
    public function getByThreadId($id)
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
