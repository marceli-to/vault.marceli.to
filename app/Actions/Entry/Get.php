<?php

namespace App\Actions\Entry;

use App\Models\Entry;
use App\Models\User;

class Get
{
	public function execute(User $user, array $filters = []): array
	{
		$query = Entry::where('user_id', $user->id)
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

		$allEntries = Entry::where('user_id', $user->id)
			->orderByDesc('created_at')
			->get()
			->map(function ($entry) {
				$entry->word_count = $entry->word_count;
				return $entry;
			});

		$counts = [
			'all' => Entry::where('user_id', $user->id)->count(),
			'idea' => Entry::where('user_id', $user->id)->where('type', 'idea')->count(),
			'link' => Entry::where('user_id', $user->id)->where('type', 'link')->count(),
			'note' => Entry::where('user_id', $user->id)->where('type', 'note')->count(),
			'pinned' => Entry::where('user_id', $user->id)->where('is_pinned', true)->count(),
		];

		$tags = Entry::where('user_id', $user->id)
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
