<?php

namespace App\Http\Requests\TimeEntry;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
	public function authorize(): bool
	{
		return true;
	}

	public function rules(): array
	{
		return [
			'project' => 'required|string|max:255',
			'description' => 'required|string',
			'date' => 'required|date',
			'actual_minutes' => 'nullable|integer|min:0',
			'estimated_minutes' => 'nullable|integer|min:0',
			'billable' => 'boolean',
			'status' => 'required|in:open,processed',
			'tags' => 'nullable|array',
		];
	}
}
