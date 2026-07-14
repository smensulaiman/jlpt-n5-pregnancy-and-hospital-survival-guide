<?php

/**
 * Import kuroshiro furigana back into the *_ruby columns, and convert
 * vocab words with author-curated 「漢字（かな）」readings to ruby directly.
 */

require 'C:/Users/sulai/Herd/japan-pregnancy-and-hospital-survival-guide/vendor/autoload.php';
$app = require 'C:/Users/sulai/Herd/japan-pregnancy-and-hospital-survival-guide/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// 1. Machine-generated furigana from the node step
$items = json_decode(file_get_contents(__DIR__ . '/ja_ruby.json'), true);
$machine = 0;

foreach ($items as $item) {
    $machine += DB::table($item['table'])
        ->where('id', $item['id'])
        ->update([$item['field'] => $item['ruby']]);
}

echo "{$machine} machine-annotated rows written.\n";

// 2. Vocab entries shaped 漢字（かな）… → ruby from the curated reading
$curated = 0;

foreach (DB::table('vocab_words')->select('id', 'japanese')->get() as $row) {
    if (! preg_match('/^(.+?)（(.+?)）(.*)$/u', $row->japanese, $m)) {
        continue;
    }
    if (! preg_match('/\p{Han}/u', $m[1])) {
        continue; // e.g. katakana headword with a note in parens
    }

    $ruby = '<ruby>' . $m[1] . '<rp>（</rp><rt>' . $m[2] . '</rt><rp>）</rp></ruby>' . $m[3];
    $curated += DB::table('vocab_words')->where('id', $row->id)->update(['japanese_ruby' => $ruby]);
}

echo "{$curated} curated vocab rows written.\n";
