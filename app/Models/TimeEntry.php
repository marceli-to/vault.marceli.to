<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeEntry extends Model
{
	protected $fillable = [
		'user_id',
		'project',
		'description',
		'date',
		'actual_minutes',
		'estimated_minutes',
		'billable',
		'status',
		'tags',
	];

	protected $casts = [
		'tags' => 'array',
		'date' => 'date',
		'billable' => 'boolean',
		'actual_minutes' => 'integer',
		'estimated_minutes' => 'integer',
	];

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}
}
