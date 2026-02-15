<?php

namespace App\Http\Controllers;

use App\Actions\Entry\CreateEntry;
use App\Actions\Entry\DeleteEntry;
use App\Actions\Entry\UpdateEntry;
use App\Http\Requests\StoreEntryRequest;
use App\Http\Requests\UpdateEntryRequest;
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

	public function store(StoreEntryRequest $request)
	{
		(new CreateEntry)->execute($request->user(), $request->validated());

		return redirect()->back();
	}

	public function update(UpdateEntryRequest $request, Entry $entry)
	{
		(new UpdateEntry)->execute($entry, $request->validated());

		return redirect()->back();
	}

	public function destroy(Request $request, Entry $entry)
	{
		if ($entry->user_id !== $request->user()->id) {
			abort(403);
		}

		(new DeleteEntry)->execute($entry);

		return redirect()->back();
	}
}
