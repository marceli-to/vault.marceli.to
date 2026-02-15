<?php

namespace App\Http\Controllers\Api;

use App\Actions\Entry\Create as CreateAction;
use App\Actions\Entry\Delete as DeleteAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Entry\Filter as FilterRequest;
use App\Http\Requests\Entry\Store as StoreRequest;
use App\Models\Entry;
use App\Models\User;

class EntryApiController extends Controller
{
	private function getUser(): User
	{
		return User::first();
	}

	public function index(FilterRequest $request)
	{
		$query = Entry::where('user_id', $this->getUser()->id)
			->orderByDesc('created_at');

		if ($search = $request->validated('search')) {
			$query->where(function ($q) use ($search) {
				$q->where('title', 'like', "%{$search}%")
				  ->orWhere('content', 'like', "%{$search}%");
			});
		}

		if ($type = $request->validated('type')) {
			$query->where('type', $type);
		}

		return response()->json($query->get());
	}

	public function show(Entry $entry)
	{
		return response()->json($entry);
	}

	public function store(StoreRequest $request)
	{
		$entry = (new CreateAction)->execute($this->getUser(), $request->validated());

		return response()->json($entry, 201);
	}

	public function destroy(Entry $entry)
	{
		(new DeleteAction)->execute($entry);

		return response()->json(['message' => 'Deleted']);
	}
}
