<?php

namespace App\Actions\Entry;

use App\Models\Entry;
use App\Models\User;

class CreateEntry
{
	public function execute(User $user, array $data): Entry
	{
		return Entry::create([
			...$data,
			'user_id' => $user->id,
			'tags' => $data['tags'] ?? [],
		]);
	}
}
