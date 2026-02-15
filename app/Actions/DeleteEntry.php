<?php

namespace App\Actions;

use App\Models\Entry;

class DeleteEntry
{
	public function execute(Entry $entry): void
	{
		$entry->delete();
	}
}
