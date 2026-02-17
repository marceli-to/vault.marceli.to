<?php

namespace App\Actions\TimeEntry;

use App\Models\TimeEntry;
use App\Models\User;

class Get
{
	public function execute(User $user, array $filters = []): array
	{
		$baseQuery = TimeEntry::where('user_id', $user->id);

		$query = clone $baseQuery;

		if ($status = $filters['status'] ?? null) {
			$query->where('status', $status);
		}

		if ($project = $filters['project'] ?? null) {
			$query->where('project', $project);
		}

		if (isset($filters['billable'])) {
			$query->where('billable', (bool) $filters['billable']);
		}

		if ($dateFrom = $filters['date_from'] ?? null) {
			$query->where('date', '>=', $dateFrom);
		}

		if ($dateTo = $filters['date_to'] ?? null) {
			$query->where('date', '<=', $dateTo);
		}

		$timeEntries = $query
			->orderByDesc('date')
			->orderByDesc('created_at')
			->get();

		// Counts
		$totals = (clone $baseQuery)
			->selectRaw('COUNT(*) as all_count')
			->selectRaw("SUM(CASE WHEN status = 'open' THEN 1 ELSE 0 END) as open_count")
			->selectRaw("SUM(CASE WHEN status = 'processed' THEN 1 ELSE 0 END) as processed_count")
			->selectRaw('SUM(CASE WHEN billable = 1 THEN 1 ELSE 0 END) as billable_count')
			->first();

		$counts = [
			'all' => (int) ($totals->all_count ?? 0),
			'open' => (int) ($totals->open_count ?? 0),
			'processed' => (int) ($totals->processed_count ?? 0),
			'billable' => (int) ($totals->billable_count ?? 0),
		];

		// Get unique projects with counts
		$projects = (clone $baseQuery)
			->selectRaw('project, COUNT(*) as count')
			->selectRaw('SUM(actual_minutes) as total_actual_minutes')
			->selectRaw('SUM(estimated_minutes) as total_estimated_minutes')
			->groupBy('project')
			->orderByDesc('count')
			->get()
			->map(fn ($p) => [
				'name' => $p->project,
				'count' => $p->count,
				'total_actual_minutes' => (int) ($p->total_actual_minutes ?? 0),
				'total_estimated_minutes' => (int) ($p->total_estimated_minutes ?? 0),
			])
			->all();

		// Project summary for current filter
		$projectSummary = $query
			->selectRaw('project')
			->selectRaw('SUM(actual_minutes) as total_actual_minutes')
			->selectRaw('SUM(estimated_minutes) as total_estimated_minutes')
			->selectRaw('COUNT(*) as entry_count')
			->selectRaw("SUM(CASE WHEN billable = 1 THEN actual_minutes ELSE 0 END) as billable_actual_minutes")
			->selectRaw("SUM(CASE WHEN billable = 1 THEN estimated_minutes ELSE 0 END) as billable_estimated_minutes")
			->groupBy('project')
			->orderByDesc('total_actual_minutes')
			->get()
			->map(fn ($p) => [
				'project' => $p->project,
				'entry_count' => $p->entry_count,
				'total_actual_minutes' => (int) ($p->total_actual_minutes ?? 0),
				'total_estimated_minutes' => (int) ($p->total_estimated_minutes ?? 0),
				'billable_actual_minutes' => (int) ($p->billable_actual_minutes ?? 0),
				'billable_estimated_minutes' => (int) ($p->billable_estimated_minutes ?? 0),
			])
			->all();

		return [
			'timeEntries' => $timeEntries,
			'counts' => $counts,
			'projects' => $projects,
			'projectSummary' => $projectSummary,
		];
	}
}
