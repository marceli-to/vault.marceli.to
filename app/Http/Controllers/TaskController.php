<?php

namespace App\Http\Controllers;

use App\Actions\Task\Create;
use App\Actions\Task\Delete;
use App\Actions\Task\Index;
use App\Actions\Task\Update;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{
	public function index(Request $request)
	{
		$filters = $request->only(['status', 'priority']);
		$data = (new Index)->execute($request->user(), $filters);

		return Inertia::render('Tasks', [
			...$data,
			'filters' => $filters,
		]);
	}

	public function store(StoreTaskRequest $request)
	{
		(new Create)->execute($request->user(), $request->validated());

		return redirect()->back();
	}

	public function update(UpdateTaskRequest $request, Task $task)
	{
		(new Update)->execute($task, $request->validated());

		return redirect()->back();
	}

	public function destroy(Request $request, Task $task)
	{
		if ($task->user_id !== $request->user()->id) {
			abort(403);
		}

		(new Delete)->execute($task);

		return redirect()->back();
	}
}
