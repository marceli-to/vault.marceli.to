<?php

namespace App\Actions\Entry;

use App\Models\Entry;

class Update
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
