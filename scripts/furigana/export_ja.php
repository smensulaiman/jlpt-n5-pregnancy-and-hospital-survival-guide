<?php

/** Export all Japanese text needing furigana to JSON for the node/kuroshiro step. */

require 'C:/Users/sulai/Herd/japan-pregnancy-and-hospital-survival-guide/vendor/autoload.php';
$app = require 'C:/Users/sulai/Herd/japan-pregnancy-and-hospital-survival-guide/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$items = [];
$han = '/\p{Han}/u';

foreach (DB::table('dialogue_lines')->select('id', 'japanese')->get() as $row) {
    if (preg_match($han, $row->japanese)) {
        $items[] = ['table' => 'dialogue_lines', 'id' => $row->id, 'field' => 'japanese_ruby', 'kind' => 'text', 'text' => $row->japanese];
    }
}

// vocab words WITHOUT a （reading）— the paren ones are handled in PHP on import
foreach (DB::table('vocab_words')->select('id', 'japanese')->get() as $row) {
    if (! str_contains($row->japanese, '（') && preg_match($han, $row->japanese)) {
        $items[] = ['table' => 'vocab_words', 'id' => $row->id, 'field' => 'japanese_ruby', 'kind' => 'text', 'text' => $row->japanese];
    }
}

foreach (DB::table('sections')->select('id', 'title_jp')->get() as $row) {
    if ($row->title_jp !== null && preg_match($han, $row->title_jp)) {
        $items[] = ['table' => 'sections', 'id' => $row->id, 'field' => 'title_jp_ruby', 'kind' => 'text', 'text' => $row->title_jp];
    }
}

foreach (DB::table('chapters')->select('id', 'title_jp')->get() as $row) {
    if (preg_match($han, $row->title_jp)) {
        $items[] = ['table' => 'chapters', 'id' => $row->id, 'field' => 'title_jp_ruby', 'kind' => 'text', 'text' => $row->title_jp];
    }
}

foreach (DB::table('parts')->select('id', 'title_jp')->get() as $row) {
    if (preg_match($han, $row->title_jp)) {
        $items[] = ['table' => 'parts', 'id' => $row->id, 'field' => 'title_jp_ruby', 'kind' => 'text', 'text' => $row->title_jp];
    }
}

foreach (DB::table('content_blocks')->select('id', 'body_html')->whereNotNull('body_html')->get() as $row) {
    if (preg_match($han, $row->body_html)) {
        $items[] = ['table' => 'content_blocks', 'id' => $row->id, 'field' => 'body_ruby_html', 'kind' => 'html', 'text' => $row->body_html];
    }
}

file_put_contents(__DIR__ . '/ja_export.json', json_encode($items, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
echo count($items) . " items exported.\n";
