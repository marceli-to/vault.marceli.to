<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEntryRequest extends FormRequest
{
	public function authorize(): bool
	{
		return true;
	}

	public function rules(): array
	{
		return [
			'title' => 'nullable|string|max:255',
			'content' => 'required|string',
			'url' => 'nullable|url|max:2048',
			'type' => 'required|in:idea,link,note',
			'tags' => 'nullable|array',
			'tags.*' => 'string|max:50',
			'is_pinned' => 'boolean',
		];
	}
}
