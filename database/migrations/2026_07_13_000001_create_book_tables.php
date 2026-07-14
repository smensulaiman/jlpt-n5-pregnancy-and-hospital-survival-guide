<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parts', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('number');
            $table->string('title_en');
            $table->string('title_jp');
            $table->timestamps();
        });

        Schema::create('chapters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('part_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('number')->unique();
            $table->string('slug')->unique();
            $table->string('title_en');
            $table->string('title_jp');
            $table->string('title_romaji')->nullable();
            $table->string('kanji_label');   // 第一章
            $table->string('kicker');        // Chapter One
            $table->timestamps();
        });

        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chapter_id')->constrained()->cascadeOnDelete();
            $table->string('number');        // "1.1", "20.25"
            $table->string('title_en');
            $table->string('title_jp')->nullable();
            $table->unsignedSmallInteger('sort');
            $table->timestamps();
        });

        Schema::create('content_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('sort');
            // paragraph | scene | dialogue | vocab | note | culture_note
            $table->string('type', 20);
            $table->string('title')->nullable();   // note-title text
            $table->text('body_html')->nullable(); // inner HTML for paragraph/scene/note bodies
            $table->timestamps();
        });

        Schema::create('dialogue_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_block_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('sort');
            $table->string('speaker_label');       // "Husband", "Nurse", "You", "Mother-in-law"
            $table->string('speaker_type', 20);    // husband|wife|nurse|doctor|midwife|staff|other
            $table->text('japanese');
            $table->text('romaji');
            $table->text('english');
            $table->timestamps();
        });

        Schema::create('vocab_words', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_block_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('sort');
            $table->string('japanese');
            $table->string('romaji');
            $table->string('english');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vocab_words');
        Schema::dropIfExists('dialogue_lines');
        Schema::dropIfExists('content_blocks');
        Schema::dropIfExists('sections');
        Schema::dropIfExists('chapters');
        Schema::dropIfExists('parts');
    }
};
