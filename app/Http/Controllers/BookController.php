<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Part;

class BookController extends Controller
{
    /** Usable height of one printed A4 content area, in mm. */
    private const PAGE_MM = 250;

    public function index()
    {
        $parts = Part::with('chapters')->orderBy('number')->get();
        $pages = $this->chapterStartPages();

        return view('book.index', compact('parts', 'pages'));
    }

    public function chapter(Chapter $chapter)
    {
        $chapter->load([
            'part',
            'sections.blocks.dialogueLines',
            'sections.blocks.vocabWords',
        ]);

        $previous = Chapter::where('number', $chapter->number - 1)->first();
        $next = Chapter::where('number', $chapter->number + 1)->first();
        $page = $this->chapterStartPages()[$chapter->number] ?? null;

        return view('book.chapter', compact('chapter', 'previous', 'next', 'page'));
    }

    /**
     * Estimated starting page for each chapter, keyed by chapter number.
     * Front matter (cover, foreword, contents) is numbered in roman
     * numerals, so chapter 1 opens the arabic sequence at page 1.
     *
     * @return array<int, int>
     */
    private function chapterStartPages(): array
    {
        $chapters = Chapter::with(['sections.blocks' => fn ($query) => $query->withCount(['dialogueLines', 'vocabWords'])])
            ->orderBy('number')
            ->get();

        $pages = [];
        $nextPage = 1;

        foreach ($chapters as $chapter) {
            $pages[$chapter->number] = $nextPage;
            $nextPage += $this->estimatedPageCount($chapter);
        }

        return $pages;
    }

    /** Approximate printed length of a chapter from its content volume. */
    private function estimatedPageCount(Chapter $chapter): int
    {
        $mm = 42; // chapter head + footer

        foreach ($chapter->sections as $section) {
            $mm += 13; // section head

            foreach ($section->blocks as $block) {
                // row heights include furigana headroom
                $mm += match ($block->type) {
                    'dialogue' => 11 + 15 * $block->dialogue_lines_count,
                    'vocab' => 11 + 9 * $block->vocab_words_count,
                    'note', 'culture_note' => 26,
                    'scene' => 7,
                    default => 16,
                };
            }
        }

        return max(1, (int) ceil($mm / self::PAGE_MM));
    }
}
