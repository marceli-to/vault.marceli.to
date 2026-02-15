<?php

namespace App\Actions\Task;

use App\Models\Task;

class Delete
{
	public function execute(Task $task): void
	{
		$task->delete();
	}
}
