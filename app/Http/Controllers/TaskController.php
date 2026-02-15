<?php

namespace App\Http\Controllers;

use App\Actions\Task\CreateTask;
use App\Actions\Task\DeleteTask;
use App\Actions\Task\ListTasks;
use App\Actions\Task\UpdateTask;
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
		$data = (new ListTasks)->execute($request->user(), $filters);

		return Inertia::render('Tasks', [
			...$data,
			'filters' => $filters,
		]);
	}

	public function store(StoreTaskRequest $request)
	{
		(new CreateTask)->execute($request->user(), $request->validated());

		return redirect()->back();
	}

	public function update(UpdateTaskRequest $request, Task $task)
	{
		(new UpdateTask)->execute($task, $request->validated());

		return redirect()->back();
	}

	public function destroy(Request $request, Task $task)
	{
		if ($task->user_id !== $request->user()->id) {
			abort(403);
		}

		(new DeleteTask)->execute($task);

		return redirect()->back();
	}
}
