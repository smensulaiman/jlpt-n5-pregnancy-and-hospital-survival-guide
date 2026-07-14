@extends('layouts.book')

@section('title', 'Chapter ' . $chapter->number . ' — ' . $chapter->title_en . ' | JLPT N5 Hospital Guide')

@section('content')

<nav class="chrome" aria-label="Book navigation">
    <a href="{{ route('book.index') }}">&larr; Contents</a>
    <span class="chrome-label">Chapter {{ $chapter->number }} of 20</span>
    <span class="chrome-group">
        <button type="button" class="print-btn" onclick="window.print()">Print &middot; 印刷</button>
        @if($next)
            <a href="{{ route('book.chapter', $next) }}">Chapter {{ $next->number }} &rarr;</a>
        @endif
    </span>
</nav>

<main class="sheet">

    <header class="chapter-head">
        <div class="ch-main">
            <p class="ch-kicker">{{ $chapter->kicker }}</p>
            <h1>{{ $chapter->title_en }}</h1>
            <p class="ch-jp">
                <span lang="ja">{!! $chapter->title_jp_ruby ?? e($chapter->title_jp) !!}</span>
                <span class="romaji">&mdash; {{ $chapter->title_romaji }}</span>
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

<nav class="chrome chrome--bottom" aria-label="Book navigation">
    @if($previous)
        <a href="{{ route('book.chapter', $previous) }}">&larr; Chapter {{ $previous->number }}: {{ $previous->title_en }}</a>
    @else
        <a href="{{ route('book.index') }}">&larr; Contents</a>
    @endif
    <span class="chrome-label" lang="ja">{{ $chapter->title_jp }}</span>
    @if($next)
        <a href="{{ route('book.chapter', $next) }}">Chapter {{ $next->number }}: {{ $next->title_en }} &rarr;</a>
    @else
        <a href="{{ route('book.index') }}">Contents &rarr;</a>
    @endif
</nav>

@endsection
