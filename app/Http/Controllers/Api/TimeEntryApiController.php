<?php

namespace App\Http\Controllers\Api;

use App\Actions\TimeEntry\Create as CreateAction;
use App\Actions\TimeEntry\Delete as DeleteAction;
use App\Actions\TimeEntry\Update as UpdateAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\TimeEntry\Filter as FilterRequest;
use App\Http\Requests\TimeEntry\Store as StoreRequest;
use App\Http\Requests\TimeEntry\Update as UpdateRequest;
use App\Models\TimeEntry;

class TimeEntryApiController extends Controller
{

	public function index(FilterRequest $request)
	{
		$perPage = (int) ($request->validated('per_page') ?? 50);

		$query = TimeEntry::where('user_id', $request->user()->id);

		if ($status = $request->validated('status')) {
			$query->where('status', $status);
		}

		if ($project = $request->validated('project')) {
			$query->where('project', $project);
		}

		if ($request->has('billable')) {
			$query->where('billable', (bool) $request->validated('billable'));
		}

		if ($dateFrom = $request->validated('date_from')) {
			$query->where('date', '>=', $dateFrom);
		}

		if ($dateTo = $request->validated('date_to')) {
			$query->where('date', '<=', $dateTo);
		}

		$timeEntries = $query
			->orderByDesc('date')
			->orderByDesc('created_at')
			->paginate($perPage);

		return response()->json($timeEntries);
	}

	public function show(TimeEntry $timeEntry)
	{
		return response()->json($timeEntry);
	}

	public function store(StoreRequest $request)
	{
		$timeEntry = (new CreateAction)->execute($request->user(), $request->validated());

		return response()->json($timeEntry, 201);
	}

	public function update(UpdateRequest $request, TimeEntry $timeEntry)
	{
		(new UpdateAction)->execute($timeEntry, $request->validated());

		return response()->json($timeEntry->fresh());
	}

	public function destroy(TimeEntry $timeEntry)
	{
		(new DeleteAction)->execute($timeEntry);

		return response()->json(['message' => 'Deleted']);
	}
}
