<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
	public function authorize(): bool
	{
		return $this->route('task')->user_id === $this->user()->id;
	}

	public function rules(): array
	{
		return [
			'title' => 'required|string|max:255',
			'description' => 'nullable|string',
			'status' => 'required|in:open,in_progress,done',
			'priority' => 'required|in:low,normal,high',
			'due_date' => 'nullable|date',
		];
	}
}
