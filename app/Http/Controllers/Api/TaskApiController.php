<?php

namespace App\Http\Controllers\Api;

use App\Actions\Task\Create as CreateAction;
use App\Actions\Task\Delete as DeleteAction;
use App\Actions\Task\Update as UpdateAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Task\Filter as FilterRequest;
use App\Http\Requests\Task\Store as StoreRequest;
use App\Http\Requests\Task\Update as UpdateRequest;
use App\Models\Task;
use App\Models\User;

class TaskApiController extends Controller
{
	private function getUser(): User
	{
		return User::first();
	}

	public function index(FilterRequest $request)
	{
		$query = Task::where('user_id', $this->getUser()->id);

		if ($status = $request->validated('status')) {
			$query->where('status', $status);
		}

		if ($priority = $request->validated('priority')) {
			$query->where('priority', $priority);
		}

		$tasks = $query->orderByRaw("CASE status WHEN 'in_progress' THEN 0 WHEN 'open' THEN 1 WHEN 'done' THEN 2 END")
			->orderByRaw("CASE priority WHEN 'high' THEN 0 WHEN 'normal' THEN 1 WHEN 'low' THEN 2 END")
			->orderByDesc('created_at')
			->get();

		return response()->json($tasks);
	}

	public function show(Task $task)
	{
		return response()->json($task);
	}

	public function store(StoreRequest $request)
	{
		$task = (new CreateAction)->execute($this->getUser(), $request->validated());

		return response()->json($task, 201);
	}

	public function update(UpdateRequest $request, Task $task)
	{
		(new UpdateAction)->execute($task, $request->validated());

		return response()->json($task->fresh());
	}

	public function destroy(Task $task)
	{
		(new DeleteAction)->execute($task);

		return response()->json(['message' => 'Deleted']);
	}
}
