<?php

namespace App\Model;

use Nette;


/**
 * Thread management.
 */
class ThreadRepository extends Repository
{

	public function get($id, $userId)
	{
		$result = $this->database->query("
			SELECT
				thread.*,
				r.replies AS replies_read
			FROM thread
			LEFT JOIN `read` r ON r.thread_id = thread.thread_id AND r.user_id = {$userId}
			WHERE thread.thread_id = {$id}
			LIMIT 1
		");

		return $result->fetch();
	}

	public function getLast($userId, $sortBy, $perPage)
	{
		$result = $this->database->query("SELECT DISTINCT
				thread.*,
				user.nick AS submiter_name,
				user2.nick AS last_reply_by_name,
				r.replies AS replies_read
			FROM thread
			JOIN user ON user.user_id = thread.submiter_id
			JOIN user AS user2 ON user2.user_id = thread.last_reply_by
			LEFT JOIN `read` r ON r.thread_id = thread.thread_id AND r.user_id = {$userId}
			ORDER BY {$sortBy} DESC
			LIMIT {$perPage}
		");

		return $result;
	}

	public function search($query, $sortBy, $perPage)
	{
		$result = NULL;
		
		if ($query)
		{
			$query = trim($this->database->connection->quote($query), "'");
			$query = str_replace('*', '%', "%{$query}%");

			$result = $this->database->query("SELECT DISTINCT
					thread.*,
					user.nick AS submiter_name,
					user2.nick AS last_reply_by_name,
					thread.replies_count AS replies_read
				FROM thread
				JOIN user ON user.user_id = thread.submiter_id
				JOIN user AS user2 ON user2.user_id = thread.last_reply_by
				JOIN message m ON m.thread_id = thread.thread_id
				WHERE thread.subject LIKE '$query' OR m.text LIKE '$query'
				ORDER BY {$sortBy} DESC
				LIMIT {$perPage}
			");
		}

		return $result;
	}

	public function add($data)
	{
		$time = new \DateTime;
		$thread = $this->database->table('thread')->insert(array(
			'submiter_id' => $data->submiter_id,
			'subject' => stripslashes($data->subject),
			'create_time' => $time,
			'last_reply_time' => $time,
			'last_reply_by' => $data->submiter_id,
			'replies_count' => 0
		));
		$this->database->table('message')->insert(array(
			'thread_id' => $thread->thread_id,
			'submiter_id' => $data->submiter_id,
			'text' => $data->text,
			'create_time' => $time
		));
		return $thread->thread_id;
	}

	public function addMessage($data)
	{
		$this->database->table('message')->insert($data);

		$thread = $this->database->table('thread')->get($data->thread_id);
		$thread->update(array(
			'last_reply_time' => new \DateTime,
			'last_reply_by' => $data->submiter_id,
			'replies_count' => $thread->replies_count + 1
		));

		return $thread->replies_count;
	}

	public function updateRead($thread, $userId)
	{
		if ($thread->replies_read === NULL)
		{
			$this->database->table('read')->insert(array(
				'user_id' => $userId,
				'thread_id' => $thread->thread_id,
				'replies' => $thread->replies_count
			));
		}
		elseif ($thread->replies_count > $thread->replies_read)
		{
			$this->database->table('read')
				->where('user_id', $userId)
				->where('thread_id', $thread->thread_id)
				->update(array(
					'replies' => $thread->replies_count
				));
		}
	}

}
