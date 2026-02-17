<?php

namespace App\Http\Controllers;

use App\Actions\TimeEntry\Create as CreateAction;
use App\Actions\TimeEntry\Delete as DeleteAction;
use App\Actions\TimeEntry\Get as GetAction;
use App\Actions\TimeEntry\Update as UpdateAction;
use App\Http\Requests\TimeEntry\Filter as FilterRequest;
use App\Http\Requests\TimeEntry\Store as StoreRequest;
use App\Http\Requests\TimeEntry\Update as UpdateRequest;
use App\Models\TimeEntry;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TimeEntryController extends Controller
{
	public function index(FilterRequest $request)
	{
		$filters = $request->validated();
		$data = (new GetAction)->execute($request->user(), $filters);

		return Inertia::render('TimeEntries', [
			...$data,
			'filters' => $filters,
		]);
	}

	public function store(StoreRequest $request)
	{
		(new CreateAction)->execute($request->user(), $request->validated());

		return redirect()->back();
	}

	public function update(UpdateRequest $request, TimeEntry $timeEntry)
	{
		(new UpdateAction)->execute($timeEntry, $request->validated());

		return redirect()->back();
	}

	public function destroy(Request $request, TimeEntry $timeEntry)
	{
		if ($timeEntry->user_id !== $request->user()->id) {
			abort(403);
		}

		(new DeleteAction)->execute($timeEntry);

		return redirect()->back();
	}
}
