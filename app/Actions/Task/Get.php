<?php

namespace App\Actions\Task;

use App\Models\Task;
use App\Models\User;

class Gets
{
	public function execute(User $user, array $filters = []): array
	{
		$query = Task::where('user_id', $user->id);

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

		$counts = [
			'all' => Task::where('user_id', $user->id)->count(),
			'open' => Task::where('user_id', $user->id)->where('status', 'open')->count(),
			'in_progress' => Task::where('user_id', $user->id)->where('status', 'in_progress')->count(),
			'done' => Task::where('user_id', $user->id)->where('status', 'done')->count(),
		];

		return [
			'tasks' => $tasks,
			'counts' => $counts,
		];
	}
}
