<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Entry extends Model
{
	protected $fillable = [
		'user_id', 'title', 'content', 'url', 'type', 'tags', 'is_pinned',
	];

	protected $casts = [
		'tags' => 'array',
		'is_pinned' => 'boolean',
	];

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	public function getWordCountAttribute(): int
	{
		return str_word_count(strip_tags($this->content));
	}
}
