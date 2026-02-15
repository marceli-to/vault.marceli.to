<?php

namespace App\Http\Requests\Task;

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
			'status' => 'nullable|in:open,in_progress,done',
			'priority' => 'nullable|in:low,normal,high',
		];
	}
}
