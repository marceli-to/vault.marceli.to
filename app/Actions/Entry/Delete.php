<?php

namespace App\Actions\Entry;

use App\Models\Entry;

class Delete
{
	public function execute(Entry $entry): void
	{
		$entry->delete();
	}
}
