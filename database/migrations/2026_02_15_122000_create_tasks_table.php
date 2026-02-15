<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('tasks', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id')->constrained()->cascadeOnDelete();
			$table->string('title');
			$table->text('description')->nullable();
			$table->string('status')->default('open'); // open, in_progress, done
			$table->string('priority')->default('normal'); // low, normal, high
			$table->date('due_date')->nullable();
			$table->json('tags')->default('[]');
			$table->integer('sort_order')->default(0);
			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('tasks');
	}
};
