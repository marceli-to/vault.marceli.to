<?php

namespace App\Actions\Task;

use App\Models\Task;

class Update
{
	public function execute(Task $task, array $data): Task
	{
		$task->update($data);

		return $task;
	}
}
