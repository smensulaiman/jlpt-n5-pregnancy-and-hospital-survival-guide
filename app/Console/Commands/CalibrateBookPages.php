<?php

namespace App\Console\Commands;

use App\Http\Controllers\BookController;
use App\Models\Chapter;
use Illuminate\Console\Command;

class CalibrateBookPages extends Command
{
    protected $signature = 'book:calibrate';

    protected $description = 'Measure the real printed page count of every chapter with headless Chrome and cache exact TOC page numbers. Run after editing content, then regenerate the PDF with /download/pdf?refresh=1.';

    public function handle(): int
    {
        $chapters = Chapter::orderBy('number')->get();
        $starts = [];
        $next = 1;

        foreach ($chapters as $chapter) {
            $count = $this->printedPageCount(route('book.chapter', $chapter));
            $starts[$chapter->number] = $next;
            $this->line(sprintf('Chapter %2d starts on page %3d (%d pages)', $chapter->number, $next, $count));
            $next += $count;
        }

        file_put_contents(
            BookController::pageMapPath(),
            json_encode($starts, JSON_PRETTY_PRINT)
        );

        $this->info(sprintf('%d chapters, %d pages. Exact page map written to %s', count($starts), $next - 1, BookController::pageMapPath()));
        $this->comment('Regenerate the cached PDF so its contents pick up the new numbers: /download/pdf?refresh=1');

        return self::SUCCESS;
    }

    /** Print one URL to a throwaway PDF and read the page count from its page tree. */
    private function printedPageCount(string $url): int
    {
        $file = storage_path('app/calibrate-tmp.pdf');

        app(BookController::class)->renderPdfFile($url, $file);

        preg_match_all('~/Count (\d+)~', file_get_contents($file), $matches);
        @unlink($file);

        if (empty($matches[1])) {
            $this->error("Could not read page count for $url");

            return 1;
        }

        return max(array_map('intval', $matches[1]));
    }
}
