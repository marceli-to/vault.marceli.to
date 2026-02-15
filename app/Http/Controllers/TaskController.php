<?php

namespace App\Http\Controllers;

use App\Actions\Task\Create as CreateAction;
use App\Actions\Task\Delete as DeleteAction;
use App\Actions\Task\Get as GetAction;
use App\Actions\Task\Update as UpdateAction;
use App\Http\Requests\Task\StoreRequest as StoreRequest;
use App\Http\Requests\Task\UpdateRequest as UpdateRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{
	public function index(Request $request)
	{
		$filters = $request->only(['status', 'priority']);
		$data = (new GetAction)->execute($request->user(), $filters);

		return Inertia::render('Tasks', [
			...$data,
			'filters' => $filters,
		]);
	}

	public function store(StoreRequest $request)
	{
		(new CreateAction)->execute($request->user(), $request->validated());

		return redirect()->back();
	}

	public function update(UpdateRequest $request, Task $task)
	{
		(new UpdateAction)->execute($task, $request->validated());

		return redirect()->back();
	}

	public function destroy(Request $request, Task $task)
	{
		if ($task->user_id !== $request->user()->id) {
			abort(403);
		}

		(new DeleteAction)->execute($task);

		return redirect()->back();
	}
}
