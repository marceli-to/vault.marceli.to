<?php

namespace App\Actions;

use App\Models\Entry;

class UpdateEntry
{
	public function execute(Entry $entry, array $data): Entry
	{
		$entry->update([
			...$data,
			'tags' => $data['tags'] ?? [],
		]);

		return $entry;
	}
}
