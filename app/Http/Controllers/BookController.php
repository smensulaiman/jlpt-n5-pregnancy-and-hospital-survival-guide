<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Part;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;

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

    /** The whole book on one page: front matter followed by every chapter. */
    public function print()
    {
        $parts = Part::with([
            'chapters.sections.blocks.dialogueLines',
            'chapters.sections.blocks.vocabWords',
        ])->orderBy('number')->get();

        $pages = $this->chapterStartPages();

        return view('book.print', compact('parts', 'pages'));
    }

    /**
     * Download the full book as a PDF, rendered by headless Chrome so the
     * print stylesheet, webfonts, and ruby annotations all come out exactly
     * as they do from the browser's own print dialog. The result is cached
     * in storage; append ?refresh=1 after editing content.
     */
    public function pdf(Request $request)
    {
        $file = storage_path('app/book.pdf');

        if ($request->boolean('refresh') || ! is_file($file)) {
            $this->renderPdfFile(route('book.print'), $file);
        }

        return response()->download($file, 'JLPT-N5-Pregnancy-and-Hospital-Survival-Guide.pdf');
    }

    /** Where book:calibrate stores measured chapter start pages. */
    public static function pageMapPath(): string
    {
        return storage_path('app/chapter-pages.json');
    }

    public function renderPdfFile(string $url, string $file): void
    {
        $result = Process::timeout(300)->run([
            $this->chromeBinary(),
            '--headless=new',
            '--disable-gpu',
            '--hide-scrollbars',
            '--no-pdf-header-footer',
            '--virtual-time-budget=20000',
            '--print-to-pdf=' . $file,
            $url,
        ]);

        if (! $result->successful() || ! is_file($file)) {
            abort(500, 'PDF generation failed: ' . trim($result->errorOutput()));
        }
    }

    private function chromeBinary(): string
    {
        $candidates = array_filter([
            config('services.chrome.path'),
            'C:\Program Files\Google\Chrome\Application\chrome.exe',
            'C:\Program Files (x86)\Google\Chrome\Application\chrome.exe',
            '/usr/bin/google-chrome',
            '/usr/bin/chromium',
            '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
        ]);

        foreach ($candidates as $path) {
            if (is_file($path)) {
                return $path;
            }
        }

        abort(500, 'Chrome not found. Set CHROME_PATH in .env to your Chrome or Chromium binary.');
    }

    /**
     * Starting page for each chapter, keyed by chapter number.
     * Front matter (cover, foreword, contents) is numbered in roman
     * numerals, so chapter 1 opens the arabic sequence at page 1.
     *
     * Prefers exact numbers measured by `php artisan book:calibrate`;
     * falls back to a content-volume estimate.
     *
     * @return array<int, int>
     */
    private function chapterStartPages(): array
    {
        if (is_file(self::pageMapPath())) {
            $measured = json_decode(file_get_contents(self::pageMapPath()), true);

            if (is_array($measured) && $measured !== []) {
                return array_combine(array_map('intval', array_keys($measured)), array_map('intval', $measured));
            }
        }

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

    /**
     * Approximate printed length of a chapter from its content volume.
     * Constants fitted against pagination measured from a real Chrome
     * print run (accurate to about one page per chapter).
     */
    private function estimatedPageCount(Chapter $chapter): int
    {
        $mm = 35; // chapter head + footer

        foreach ($chapter->sections as $section) {
            $mm += 18; // section head

            foreach ($section->blocks as $block) {
                // row heights include furigana headroom
                $mm += match ($block->type) {
                    'dialogue' => 16 + 17 * $block->dialogue_lines_count,
                    'vocab' => 16 + 13 * $block->vocab_words_count,
                    'note', 'culture_note' => 34,
                    'scene' => 7,
                    default => 16,
                };
            }
        }

        return max(1, (int) ceil($mm / self::PAGE_MM));
    }
}
