<?php

namespace App\Http\Controllers\Api;

use App\Actions\Task\Create as CreateAction;
use App\Actions\Task\Delete as DeleteAction;
use App\Actions\Task\Update as UpdateAction;
use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class TaskApiController extends Controller
{
	private function getUser(): User
	{
		return User::first();
	}

	public function index(Request $request)
	{
		$query = Task::where('user_id', $this->getUser()->id);

		if ($status = $request->input('status')) {
			$query->where('status', $status);
		}

		if ($priority = $request->input('priority')) {
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

	public function store(Request $request)
	{
		$validated = $request->validate([
			'title' => 'required|string|max:255',
			'description' => 'nullable|string',
			'status' => 'sometimes|in:open,in_progress,done',
			'priority' => 'sometimes|in:low,normal,high',
			'due_date' => 'nullable|date',
		]);

		$validated['status'] = $validated['status'] ?? 'open';
		$validated['priority'] = $validated['priority'] ?? 'normal';

		$task = (new CreateAction)->execute($this->getUser(), $validated);

		return response()->json($task, 201);
	}

	public function update(Request $request, Task $task)
	{
		$validated = $request->validate([
			'title' => 'sometimes|string|max:255',
			'description' => 'nullable|string',
			'status' => 'sometimes|in:open,in_progress,done',
			'priority' => 'sometimes|in:low,normal,high',
			'due_date' => 'nullable|date',
		]);

		(new UpdateAction)->execute($task, $validated);

		return response()->json($task->fresh());
	}

	public function destroy(Task $task)
	{
		(new DeleteAction)->execute($task);

		return response()->json(['message' => 'Deleted']);
	}
}
