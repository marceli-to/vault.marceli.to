<?php

namespace App\Http\Requests\TimeEntry;

use Illuminate\Foundation\Http\FormRequest;

class Filter extends FormRequest
{
	public function authorize(): bool
	{
		return true;
	}

	public function rules(): array
	{
		return [
			'status' => 'nullable|in:open,processed',
			'project' => 'nullable|string|max:255',
			'billable' => 'nullable|boolean',
			'date_from' => 'nullable|date',
			'date_to' => 'nullable|date',
			'per_page' => 'nullable|integer|min:1|max:100',
		];
	}
}
