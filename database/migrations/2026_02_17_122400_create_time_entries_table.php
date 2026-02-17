<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('time_entries', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id')->constrained()->cascadeOnDelete();
			$table->string('project');
			$table->text('description');
			$table->date('date');
			$table->integer('actual_minutes')->nullable();
			$table->integer('estimated_minutes')->nullable();
			$table->boolean('billable')->default(true);
			$table->string('status')->default('open'); // open, processed
			$table->json('tags')->default('[]');
			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('time_entries');
	}
};
