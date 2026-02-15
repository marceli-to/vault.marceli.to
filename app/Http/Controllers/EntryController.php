<?php

namespace App\Http\Controllers;

use App\Actions\Entry\Create as CreateAction;
use App\Actions\Entry\Delete as DeleteAction;
use App\Actions\Entry\Get as GetAction;
use App\Actions\Entry\Update as UpdateAction;
use App\Http\Requests\Entry\StoreRequest as StoreRequest;
use App\Http\Requests\Entry\UpdateRequest as UpdateRequest;
use App\Models\Entry;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EntryController extends Controller
{
	public function index(Request $request)
	{
		$filters = $request->only(['search', 'type', 'tag', 'pinned']);
		$data = (new GetAction)->execute($request->user(), $filters);

		return Inertia::render('Dashboard', [
			...$data,
			'filters' => $filters,
		]);
	}

	public function store(StoreRequest $request)
	{
		(new CreateAction)->execute($request->user(), $request->validated());

		return redirect()->back();
	}

	public function update(UpdateRequest $request, Entry $entry)
	{
		(new UpdateAction)->execute($entry, $request->validated());

		return redirect()->back();
	}

	public function destroy(Request $request, Entry $entry)
	{
		if ($entry->user_id !== $request->user()->id) {
			abort(403);
		}

		(new DeleteAction)->execute($entry);

		return redirect()->back();
	}
}
