<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ai_invocations', function (Blueprint $table) {
            $table->id();
            $table->uuid('invocation_id')->index();

            $table->string('agent');
            $table->string('provider')->nullable();
            $table->string('model')->nullable();

            // Which record the run was about, when it was about one at all.
            $table->nullableMorphs('subject');

            $table->unsignedInteger('prompt_tokens')->default(0);
            $table->unsignedInteger('completion_tokens')->default(0);
            $table->unsignedInteger('cache_read_tokens')->default(0);
            $table->unsignedInteger('cache_write_tokens')->default(0);
            $table->unsignedInteger('reasoning_tokens')->default(0);

            $table->unsignedInteger('duration_ms')->nullable();
            $table->boolean('failed')->default(false);
            $table->text('error')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_invocations');
    }
};
