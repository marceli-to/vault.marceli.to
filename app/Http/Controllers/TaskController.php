<?php

namespace App\Http\Controllers;

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

	public function store(Request $request)
	{
		$validated = $request->validate([
			'title' => 'required|string|max:255',
			'description' => 'nullable|string',
			'status' => 'required|in:open,in_progress,done',
			'priority' => 'required|in:low,normal,high',
			'due_date' => 'nullable|date',
		]);

		$validated['user_id'] = $request->user()->id;

		Task::create($validated);

		return redirect()->back();
	}

	public function update(Request $request, Task $task)
	{
		if ($task->user_id !== $request->user()->id) {
			abort(403);
		}

		$validated = $request->validate([
			'title' => 'required|string|max:255',
			'description' => 'nullable|string',
			'status' => 'required|in:open,in_progress,done',
			'priority' => 'required|in:low,normal,high',
			'due_date' => 'nullable|date',
		]);

		$task->update($validated);

		return redirect()->back();
	}

	public function destroy(Request $request, Task $task)
	{
		if ($task->user_id !== $request->user()->id) {
			abort(403);
		}

		$task->delete();

		return redirect()->back();
	}
}
