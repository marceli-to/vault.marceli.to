<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Entry;
use App\Models\User;
use Illuminate\Http\Request;

class EntryApiController extends Controller
{
	private function getUser(): User
	{
		return User::first();
	}

	public function index(Request $request)
	{
		$query = Entry::where('user_id', $this->getUser()->id)
			->orderByDesc('created_at');

		if ($search = $request->input('search')) {
			$query->where(function ($q) use ($search) {
				$q->where('title', 'like', "%{$search}%")
				  ->orWhere('content', 'like', "%{$search}%");
			});
		}

		if ($type = $request->input('type')) {
			$query->where('type', $type);
		}

		return response()->json($query->get());
	}

	public function show(Entry $entry)
	{
		return response()->json($entry);
	}

	public function store(Request $request)
	{
		$validated = $request->validate([
			'title' => 'nullable|string|max:255',
			'content' => 'required|string',
			'url' => 'nullable|url|max:2048',
			'type' => 'required|in:idea,link,note,quote,reference',
			'tags' => 'nullable|array',
			'tags.*' => 'string|max:50',
			'is_pinned' => 'boolean',
		]);

		$validated['user_id'] = $this->getUser()->id;
		$validated['tags'] = $validated['tags'] ?? [];

		$entry = Entry::create($validated);

		return response()->json($entry, 201);
	}

	public function destroy(Entry $entry)
	{
		$entry->delete();

		return response()->json(['message' => 'Deleted']);
	}
}
