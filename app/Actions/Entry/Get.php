<?php

namespace App\Actions\Entry;

use App\Models\Entry;
use App\Models\User;

class Get
{
	public function execute(User $user, array $filters = []): array
	{
		$baseQuery = Entry::where('user_id', $user->id);

		$query = (clone $baseQuery)
			->orderByDesc('is_pinned')
			->orderByDesc('created_at');

		if ($search = $filters['search'] ?? null) {
			$query->where(function ($q) use ($search) {
				$q->where('title', 'like', "%{$search}%")
				  ->orWhere('content', 'like', "%{$search}%");
			});
		}

		if ($type = $filters['type'] ?? null) {
			$query->where('type', $type);
		}

		if ($tag = $filters['tag'] ?? null) {
			$query->whereJsonContains('tags', $tag);
		}

		if ($filters['pinned'] ?? null) {
			$query->where('is_pinned', true);
		}

		$entries = $query->get()->map(function ($entry) {
			$entry->word_count = $entry->word_count;
			return $entry;
		});

		$allEntries = (clone $baseQuery)
			->orderByDesc('created_at')
			->get()
			->map(function ($entry) {
				$entry->word_count = $entry->word_count;
				return $entry;
			});

		$totals = (clone $baseQuery)
			->selectRaw('COUNT(*) as all_count')
			->selectRaw("SUM(CASE WHEN type = 'idea' THEN 1 ELSE 0 END) as idea_count")
			->selectRaw("SUM(CASE WHEN type = 'link' THEN 1 ELSE 0 END) as link_count")
			->selectRaw("SUM(CASE WHEN type = 'note' THEN 1 ELSE 0 END) as note_count")
			->selectRaw('SUM(CASE WHEN is_pinned = 1 THEN 1 ELSE 0 END) as pinned_count')
			->first();

		$counts = [
			'all' => (int) ($totals->all_count ?? 0),
			'idea' => (int) ($totals->idea_count ?? 0),
			'link' => (int) ($totals->link_count ?? 0),
			'note' => (int) ($totals->note_count ?? 0),
			'pinned' => (int) ($totals->pinned_count ?? 0),
		];

		$tags = (clone $baseQuery)
			->whereNotNull('tags')
			->pluck('tags')
			->flatten()
			->countBy()
			->map(fn ($count, $name) => ['name' => $name, 'count' => $count])
			->sortByDesc('count')
			->values()
			->all();

		return [
			'entries' => $entries,
			'allEntries' => $allEntries,
			'counts' => $counts,
			'tags' => $tags,
		];
	}
}
