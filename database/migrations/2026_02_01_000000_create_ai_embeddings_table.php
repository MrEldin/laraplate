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
        Schema::create('ai_embeddings', function (Blueprint $table) {
            $table->id();
            $table->morphs('embeddable');

            // The text that was embedded, plus its hash, so re-indexing can skip
            // records whose searchable text has not actually changed.
            $table->text('content');
            $table->string('content_hash', 64);

            $table->string('provider');
            $table->string('model');
            $table->unsignedSmallInteger('dimensions');
            $table->json('vector');

            $table->timestamps();

            $table->unique(['embeddable_type', 'embeddable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_embeddings');
    }
};
