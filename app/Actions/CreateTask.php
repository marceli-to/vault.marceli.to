<?php

namespace App\Actions;

use App\Models\Task;
use App\Models\User;

class CreateTask
{
	public function execute(User $user, array $data): Task
	{
		return Task::create([
			...$data,
			'user_id' => $user->id,
		]);
	}
}
