<?php

namespace App\Actions\TimeEntry;

use App\Models\TimeEntry;
use App\Models\User;

class Create
{
	public function execute(User $user, array $data): TimeEntry
	{
		return TimeEntry::create([
			...$data,
			'user_id' => $user->id,
		]);
	}
}
