<?php

namespace App\Http\Controllers;

use App\Models\Entry;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EntryController extends Controller
{
	public function index(Request $request)
	{
		$query = Entry::where('user_id', $request->user()->id)
			->orderByDesc('is_pinned')
			->orderByDesc('created_at');

		if ($search = $request->input('search')) {
			$query->where(function ($q) use ($search) {
				$q->where('title', 'like', "%{$search}%")
				  ->orWhere('content', 'like', "%{$search}%");
			});
		}

		if ($type = $request->input('type')) {
			$query->where('type', $type);
		}

		if ($tag = $request->input('tag')) {
			$query->whereJsonContains('tags', $tag);
		}

		if ($request->input('pinned')) {
			$query->where('is_pinned', true);
		}

		$entries = $query->get()->map(function ($entry) {
			$entry->word_count = $entry->word_count;
			return $entry;
		});

		$counts = [
			'all' => Entry::where('user_id', $request->user()->id)->count(),
			'idea' => Entry::where('user_id', $request->user()->id)->where('type', 'idea')->count(),
			'link' => Entry::where('user_id', $request->user()->id)->where('type', 'link')->count(),
			'note' => Entry::where('user_id', $request->user()->id)->where('type', 'note')->count(),
			'pinned' => Entry::where('user_id', $request->user()->id)->where('is_pinned', true)->count(),
		];

		$allEntries = Entry::where('user_id', $request->user()->id)
			->orderByDesc('created_at')
			->get()
			->map(function ($entry) {
				$entry->word_count = $entry->word_count;
				return $entry;
			});

		$tags = Entry::where('user_id', $request->user()->id)
			->whereNotNull('tags')
			->pluck('tags')
			->flatten()
			->countBy()
			->map(fn ($count, $name) => ['name' => $name, 'count' => $count])
			->sortByDesc('count')
			->values()
			->all();

		return Inertia::render('Dashboard', [
			'entries' => $entries,
			'allEntries' => $allEntries,
			'counts' => $counts,
			'tags' => $tags,
			'filters' => $request->only(['search', 'type', 'tag', 'pinned']),
		]);
	}

	public function store(Request $request)
	{
		$validated = $request->validate([
			'title' => 'nullable|string|max:255',
			'content' => 'required|string',
			'url' => 'nullable|url|max:2048',
			'type' => 'required|in:idea,link,note',
			'tags' => 'nullable|array',
			'tags.*' => 'string|max:50',
			'is_pinned' => 'boolean',
		]);

		$validated['user_id'] = $request->user()->id;
		$validated['tags'] = $validated['tags'] ?? [];

		Entry::create($validated);

		return redirect()->back();
	}

	public function update(Request $request, Entry $entry)
	{
		if ($entry->user_id !== $request->user()->id) {
			abort(403);
		}

		$validated = $request->validate([
			'title' => 'nullable|string|max:255',
			'content' => 'required|string',
			'url' => 'nullable|url|max:2048',
			'type' => 'required|in:idea,link,note',
			'tags' => 'nullable|array',
			'tags.*' => 'string|max:50',
			'is_pinned' => 'boolean',
		]);

		$validated['tags'] = $validated['tags'] ?? [];

		$entry->update($validated);

		return redirect()->back();
	}

	public function destroy(Request $request, Entry $entry)
	{
		if ($entry->user_id !== $request->user()->id) {
			abort(403);
		}

		$entry->delete();

		return redirect()->back();
	}
}
