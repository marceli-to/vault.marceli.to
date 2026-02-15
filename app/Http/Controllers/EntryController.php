<?php

namespace App\Http\Controllers;

use App\Actions\Entry\Create;
use App\Actions\Entry\Delete;
use App\Actions\Entry\Index;
use App\Actions\Entry\Update;
use App\Http\Requests\StoreEntryRequest;
use App\Http\Requests\UpdateEntryRequest;
use App\Models\Entry;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EntryController extends Controller
{
	public function index(Request $request)
	{
		$filters = $request->only(['search', 'type', 'tag', 'pinned']);
		$data = (new Index)->execute($request->user(), $filters);

		return Inertia::render('Dashboard', [
			...$data,
			'filters' => $filters,
		]);
	}

	public function store(StoreEntryRequest $request)
	{
		(new Create)->execute($request->user(), $request->validated());

		return redirect()->back();
	}

	public function update(UpdateEntryRequest $request, Entry $entry)
	{
		(new Update)->execute($entry, $request->validated());

		return redirect()->back();
	}

	public function destroy(Request $request, Entry $entry)
	{
		if ($entry->user_id !== $request->user()->id) {
			abort(403);
		}

		(new Delete)->execute($entry);

		return redirect()->back();
	}
}
