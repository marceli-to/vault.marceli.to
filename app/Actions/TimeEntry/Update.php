<?php

namespace App\Actions\TimeEntry;

use App\Models\TimeEntry;

class Update
{
	public function execute(TimeEntry $timeEntry, array $data): TimeEntry
	{
		$timeEntry->update($data);

		return $timeEntry;
	}
}
