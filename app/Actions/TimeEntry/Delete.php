<?php

namespace App\Actions\TimeEntry;

use App\Models\TimeEntry;

class Delete
{
	public function execute(TimeEntry $timeEntry): void
	{
		$timeEntry->delete();
	}
}
