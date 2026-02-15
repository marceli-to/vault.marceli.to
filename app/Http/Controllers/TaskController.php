<?php

namespace App\Http\Controllers;

use App\Actions\Task\CreateTask;
use App\Actions\Task\DeleteTask;
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
		$query = Task::where('user_id', $request->user()->id);

		if ($status = $request->input('status')) {
			$query->where('status', $status);
		}

		if ($priority = $request->input('priority')) {
			$query->where('priority', $priority);
		}

		$tasks = $query->orderByRaw("CASE status WHEN 'in_progress' THEN 0 WHEN 'open' THEN 1 WHEN 'done' THEN 2 END")
			->orderByRaw("CASE priority WHEN 'high' THEN 0 WHEN 'normal' THEN 1 WHEN 'low' THEN 2 END")
			->orderBy('sort_order')
			->orderByDesc('created_at')
			->get();

		$counts = [
			'all' => Task::where('user_id', $request->user()->id)->count(),
			'open' => Task::where('user_id', $request->user()->id)->where('status', 'open')->count(),
			'in_progress' => Task::where('user_id', $request->user()->id)->where('status', 'in_progress')->count(),
			'done' => Task::where('user_id', $request->user()->id)->where('status', 'done')->count(),
		];

		return Inertia::render('Tasks', [
			'tasks' => $tasks,
			'counts' => $counts,
			'filters' => $request->only(['status', 'priority']),
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
