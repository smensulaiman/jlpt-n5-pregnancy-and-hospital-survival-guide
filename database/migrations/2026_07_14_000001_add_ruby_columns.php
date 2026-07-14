<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dialogue_lines', function (Blueprint $table) {
            $table->text('japanese_ruby')->nullable()->after('japanese');
        });

        Schema::table('vocab_words', function (Blueprint $table) {
            $table->text('japanese_ruby')->nullable()->after('japanese');
        });

        Schema::table('sections', function (Blueprint $table) {
            $table->text('title_jp_ruby')->nullable()->after('title_jp');
        });

        Schema::table('chapters', function (Blueprint $table) {
            $table->text('title_jp_ruby')->nullable()->after('title_jp');
        });

        Schema::table('content_blocks', function (Blueprint $table) {
            $table->text('body_ruby_html')->nullable()->after('body_html');
        });
    }

    public function down(): void
    {
        Schema::table('dialogue_lines', fn (Blueprint $table) => $table->dropColumn('japanese_ruby'));
        Schema::table('vocab_words', fn (Blueprint $table) => $table->dropColumn('japanese_ruby'));
        Schema::table('sections', fn (Blueprint $table) => $table->dropColumn('title_jp_ruby'));
        Schema::table('chapters', fn (Blueprint $table) => $table->dropColumn('title_jp_ruby'));
        Schema::table('content_blocks', fn (Blueprint $table) => $table->dropColumn('body_ruby_html'));
    }
};
