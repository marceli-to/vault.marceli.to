<?php

namespace App\Http\Requests\Entry;

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
			'search' => 'nullable|string|max:255',
			'type' => 'nullable|in:idea,link,note',
			'tag' => 'nullable|string|max:50',
			'pinned' => 'nullable|boolean',
		];
	}
}
