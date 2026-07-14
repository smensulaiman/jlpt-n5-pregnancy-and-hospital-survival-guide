<main class="sheet{{ ($break ?? false) ? ' sheet--break' : '' }}">

    <header class="chapter-head">
        <div class="ch-main">
            <p class="ch-kicker">{{ $chapter->kicker }}</p>
            <h1>{{ $chapter->title_en }}</h1>
            <p class="ch-jp">
                <span lang="ja">{!! $chapter->title_jp_ruby ?? e($chapter->title_jp) !!}</span>
                <span class="romaji">&middot; {{ $chapter->title_romaji }}</span>
            </p>
        </div>
        <div class="ch-kanji" lang="ja" aria-hidden="true">{{ $chapter->kanji_label }}</div>
    </header>

    @foreach($chapter->sections as $section)
        <section class="book-section">
            <div class="section-head">
                <span class="sec-no">{{ $section->number }}</span>
                <h2>{{ $section->title_en }}</h2>
                @if(filled($section->title_jp))
                    <span class="sec-jp" lang="ja">{!! $section->title_jp_ruby ?? e($section->title_jp) !!}</span>
                @endif
            </div>

            @foreach($section->blocks as $block)
                @include('book.partials.block', ['block' => $block])
            @endforeach
        </section>
    @endforeach

    <footer class="book-footer">
        <span>Chapter {{ $chapter->number }} &middot; {{ $chapter->title_en }}</span>
        @if($page)
            <span class="page-no">&mdash; {{ $page }} &mdash;</span>
        @endif
        <span class="jp" lang="ja">{{ $chapter->kanji_label }}&#12539;{{ $chapter->title_jp }}</span>
    </footer>

</main>
