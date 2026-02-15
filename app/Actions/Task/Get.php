<?php

namespace App\Actions\Task;

use App\Models\Task;
use App\Models\User;

class Get
{
	public function execute(User $user, array $filters = []): array
	{
		$baseQuery = Task::where('user_id', $user->id);

		$query = clone $baseQuery;

		if ($status = $filters['status'] ?? null) {
			$query->where('status', $status);
		}

		if ($priority = $filters['priority'] ?? null) {
			$query->where('priority', $priority);
		}

		$tasks = $query->orderByRaw("CASE status WHEN 'in_progress' THEN 0 WHEN 'open' THEN 1 WHEN 'done' THEN 2 END")
			->orderByRaw("CASE priority WHEN 'high' THEN 0 WHEN 'normal' THEN 1 WHEN 'low' THEN 2 END")
			->orderBy('sort_order')
			->orderByDesc('created_at')
			->get();

		$totals = (clone $baseQuery)
			->selectRaw('COUNT(*) as all_count')
			->selectRaw("SUM(CASE WHEN status = 'open' THEN 1 ELSE 0 END) as open_count")
			->selectRaw("SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress_count")
			->selectRaw("SUM(CASE WHEN status = 'done' THEN 1 ELSE 0 END) as done_count")
			->first();

		$counts = [
			'all' => (int) ($totals->all_count ?? 0),
			'open' => (int) ($totals->open_count ?? 0),
			'in_progress' => (int) ($totals->in_progress_count ?? 0),
			'done' => (int) ($totals->done_count ?? 0),
		];

		return [
			'tasks' => $tasks,
			'counts' => $counts,
		];
	}
}
