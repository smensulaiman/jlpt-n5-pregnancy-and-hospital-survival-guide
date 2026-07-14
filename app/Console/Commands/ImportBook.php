<?php

namespace App\Console\Commands;

use App\Models\Chapter;
use App\Models\ContentBlock;
use App\Models\DialogueLine;
use App\Models\Part;
use App\Models\Section;
use App\Models\VocabWord;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportBook extends Command
{
    protected $signature = 'book:import {--fresh : Truncate book tables first}';

    protected $description = 'Import the static HTML book chapters from public/raw into the database';

    /**
     * Part definitions from the index.html table of contents.
     *
     * @var array<int, array{number: int, title_en: string, title_jp: string, chapters: array<int>}>
     */
    private array $partDefinitions = [
        ['number' => 1, 'title_en' => 'The Big Night', 'title_jp' => '出産の夜', 'chapters' => [1, 2, 3, 4, 5]],
        ['number' => 2, 'title_en' => 'Labor & Birth', 'title_jp' => '陣痛と出産', 'chapters' => [6, 7, 8, 9, 10]],
        ['number' => 3, 'title_en' => 'The Hospital Stay', 'title_jp' => '入院生活', 'chapters' => [11, 12, 13, 14, 15, 16]],
        ['number' => 4, 'title_en' => 'Life After Birth', 'title_jp' => '出産のあとの生活', 'chapters' => [17, 18, 19, 20]],
    ];

    public function handle(): int
    {
        $rawDir = public_path('raw');

        if ($this->option('fresh')) {
            $this->info('Deleting existing book data...');
            Part::query()->delete(); // FK cascades wipe chapters, sections, blocks, lines, words
        }

        $partIdByChapter = [];

        foreach ($this->partDefinitions as $definition) {
            $part = Part::create([
                'number' => $definition['number'],
                'title_en' => $definition['title_en'],
                'title_jp' => $definition['title_jp'],
            ]);

            foreach ($definition['chapters'] as $chapterNumber) {
                $partIdByChapter[$chapterNumber] = $part->id;
            }
        }

        for ($number = 1; $number <= 20; $number++) {
            $file = $rawDir.DIRECTORY_SEPARATOR.sprintf('chapter%02d.html', $number);

            if (! is_file($file)) {
                $this->error("Missing chapter file: {$file}");

                return self::FAILURE;
            }

            DB::transaction(function () use ($file, $number, $partIdByChapter) {
                $this->importChapter($file, $number, $partIdByChapter[$number]);
            });

            $this->line("Imported chapter {$number}");
        }

        $this->newLine();
        $this->info('Import complete.');
        $this->table(['Table', 'Count'], [
            ['parts', Part::count()],
            ['chapters', Chapter::count()],
            ['sections', Section::count()],
            ['content_blocks', ContentBlock::count()],
            ['dialogue_lines', DialogueLine::count()],
            ['vocab_words', VocabWord::count()],
        ]);
        $this->table(['Block type', 'Count'], ContentBlock::query()
            ->selectRaw('type, count(*) as c')
            ->groupBy('type')
            ->orderBy('type')
            ->pluck('c', 'type')
            ->map(fn ($count, $type) => [$type, $count])
            ->values()
            ->all());

        return self::SUCCESS;
    }

    private function importChapter(string $file, int $number, int $partId): void
    {
        $html = file_get_contents($file);

        $dom = new DOMDocument;
        $dom->loadHTML('<?xml encoding="utf-8"?>'.$html, LIBXML_NOERROR);
        $xpath = new DOMXPath($dom);

        $titleEn = $this->text($xpath, '//header[contains(@class,"chapter-head")]/h1');
        $kanjiLabel = $this->text($xpath, '//header[contains(@class,"chapter-head")]//div[@class="ch-kanji"]');
        $kicker = $this->text($xpath, '//header[contains(@class,"chapter-head")]//p[@class="ch-kicker"]');

        $chJp = $xpath->query('//header[contains(@class,"chapter-head")]//p[@class="ch-jp"]')->item(0);
        [$titleJp, $titleRomaji] = $this->splitJapaneseTitle($chJp);

        $chapter = Chapter::create([
            'part_id' => $partId,
            'number' => $number,
            'slug' => Str::slug($titleEn),
            'title_en' => $titleEn,
            'title_jp' => $titleJp,
            'title_romaji' => $titleRomaji,
            'kanji_label' => $kanjiLabel,
            'kicker' => $kicker,
        ]);

        $sectionSort = 0;

        foreach ($xpath->query('//body//section') as $sectionNode) {
            $this->importSection($xpath, $sectionNode, $chapter, ++$sectionSort);
        }
    }

    private function importSection(DOMXPath $xpath, DOMElement $sectionNode, Chapter $chapter, int $sort): void
    {
        $h2 = $xpath->query('.//h2', $sectionNode)->item(0);

        $secNo = $this->normalize($xpath->query('.//span[@class="sec-no"]', $h2)->item(0)?->textContent ?? '');
        $jpNode = $xpath->query('.//span[@class="jp"]', $h2)->item(0);
        $titleJp = $jpNode ? trim($jpNode->textContent) : null;

        // title_en = h2 text minus the sec-no and jp spans
        $titleEn = '';
        foreach ($h2->childNodes as $child) {
            if ($child instanceof DOMElement && in_array($child->getAttribute('class'), ['sec-no', 'jp'], true)) {
                continue;
            }
            $titleEn .= $child->textContent;
        }
        $titleEn = $this->normalize($titleEn);

        $section = Section::create([
            'chapter_id' => $chapter->id,
            'number' => $secNo,
            'title_en' => $titleEn,
            'title_jp' => $titleJp !== '' ? $titleJp : null,
            'sort' => $sort,
        ]);

        $blockSort = 0;

        foreach ($sectionNode->childNodes as $node) {
            if (! $node instanceof DOMElement) {
                continue;
            }

            $classes = preg_split('/\s+/', trim($node->getAttribute('class')), -1, PREG_SPLIT_NO_EMPTY);

            if ($node->nodeName === 'p') {
                $type = in_array('scene', $classes, true) ? 'scene' : 'paragraph';
                ContentBlock::create([
                    'section_id' => $section->id,
                    'sort' => ++$blockSort,
                    'type' => $type,
                    'body_html' => trim($this->innerHtml($node)),
                ]);
            } elseif ($node->nodeName === 'table' && in_array('dialogue', $classes, true)) {
                $block = ContentBlock::create([
                    'section_id' => $section->id,
                    'sort' => ++$blockSort,
                    'type' => 'dialogue',
                ]);
                $this->importDialogueRows($xpath, $node, $block);
            } elseif ($node->nodeName === 'table' && in_array('vocab', $classes, true)) {
                $block = ContentBlock::create([
                    'section_id' => $section->id,
                    'sort' => ++$blockSort,
                    'type' => 'vocab',
                ]);
                $this->importVocabRows($xpath, $node, $block);
            } elseif ($node->nodeName === 'div' && in_array('note', $classes, true)) {
                $type = in_array('culture', $classes, true) ? 'culture_note' : 'note';
                $title = null;
                $bodyHtml = '';
                $nestedTables = [];

                foreach ($node->childNodes as $child) {
                    if ($child instanceof DOMElement && $child->nodeName === 'p' && $child->getAttribute('class') === 'note-title') {
                        $title = $this->normalize($child->textContent);

                        continue;
                    }

                    // Chapter 9 nests a dialogue table inside a note; extract
                    // nested dialogue/vocab tables as their own blocks.
                    if ($child instanceof DOMElement && $child->nodeName === 'table') {
                        $childClasses = preg_split('/\s+/', trim($child->getAttribute('class')), -1, PREG_SPLIT_NO_EMPTY);

                        if (in_array('dialogue', $childClasses, true) || in_array('vocab', $childClasses, true)) {
                            $nestedTables[] = [$child, in_array('dialogue', $childClasses, true) ? 'dialogue' : 'vocab'];

                            continue;
                        }
                    }

                    $bodyHtml .= $node->ownerDocument->saveHTML($child);
                }

                $bodyHtml = trim($bodyHtml);

                ContentBlock::create([
                    'section_id' => $section->id,
                    'sort' => ++$blockSort,
                    'type' => $type,
                    'title' => $title,
                    'body_html' => $bodyHtml !== '' ? $bodyHtml : null,
                ]);

                foreach ($nestedTables as [$table, $tableType]) {
                    $block = ContentBlock::create([
                        'section_id' => $section->id,
                        'sort' => ++$blockSort,
                        'type' => $tableType,
                    ]);

                    $tableType === 'dialogue'
                        ? $this->importDialogueRows($xpath, $table, $block)
                        : $this->importVocabRows($xpath, $table, $block);
                }
            }
        }
    }

    private function importDialogueRows(DOMXPath $xpath, DOMElement $table, ContentBlock $block): void
    {
        $sort = 0;

        foreach ($xpath->query('.//tbody/tr', $table) as $tr) {
            $cells = $xpath->query('./td', $tr);

            if ($cells->length < 4) {
                continue;
            }

            $speakerCell = $cells->item(0);
            $speakerType = 'other';

            foreach (preg_split('/\s+/', $speakerCell->getAttribute('class'), -1, PREG_SPLIT_NO_EMPTY) as $class) {
                if (str_starts_with($class, 'spk-')) {
                    $speakerType = substr($class, 4);
                }
            }

            DialogueLine::create([
                'content_block_id' => $block->id,
                'sort' => ++$sort,
                'speaker_label' => $this->normalize($speakerCell->textContent),
                'speaker_type' => $speakerType,
                'japanese' => trim($cells->item(1)->textContent),
                'romaji' => $this->normalize($cells->item(2)->textContent),
                'english' => $this->normalize($cells->item(3)->textContent),
            ]);
        }
    }

    private function importVocabRows(DOMXPath $xpath, DOMElement $table, ContentBlock $block): void
    {
        $sort = 0;

        foreach ($xpath->query('.//tbody/tr', $table) as $tr) {
            $cells = $xpath->query('./td', $tr);

            if ($cells->length < 3) {
                continue;
            }

            VocabWord::create([
                'content_block_id' => $block->id,
                'sort' => ++$sort,
                'japanese' => trim($cells->item(0)->textContent),
                'romaji' => $this->normalize($cells->item(1)->textContent),
                'english' => $this->normalize($cells->item(2)->textContent),
            ]);
        }
    }

    /**
     * Split the ch-jp paragraph into the Japanese title and the romaji reading.
     *
     * @return array{0: string, 1: ?string}
     */
    private function splitJapaneseTitle(?DOMNode $chJp): array
    {
        if (! $chJp) {
            return ['', null];
        }

        $titleJp = '';
        $romaji = null;

        foreach ($chJp->childNodes as $child) {
            if ($child instanceof DOMElement && $child->getAttribute('class') === 'romaji') {
                $romaji = $this->normalize(preg_replace('/^[—–-]\s*/u', '', trim($child->textContent)));

                continue;
            }
            $titleJp .= $child->textContent;
        }

        return [trim($titleJp), $romaji !== '' ? $romaji : null];
    }

    private function text(DOMXPath $xpath, string $query): string
    {
        return $this->normalize($xpath->query($query)->item(0)?->textContent ?? '');
    }

    private function innerHtml(DOMNode $node): string
    {
        $html = '';

        foreach ($node->childNodes as $child) {
            $html .= $node->ownerDocument->saveHTML($child);
        }

        return $html;
    }

    private function normalize(string $text): string
    {
        return trim(preg_replace('/\s+/u', ' ', $text));
    }
}
