@extends('layouts.book')

@section('title', 'Chapter ' . $chapter->number . ': ' . $chapter->title_en . ' | JLPT N5 Hospital Guide')

@section('content')

<nav class="chrome" aria-label="Book navigation">
    <a href="{{ route('book.index') }}">&larr; Contents</a>
    <span class="chrome-label">Chapter {{ $chapter->number }} of 20</span>
    <span class="chrome-group">
        <a class="pdf-btn" href="{{ route('book.pdf') }}">Download PDF</a>
        <button type="button" class="print-btn" onclick="window.print()">Print</button>
        @if($next)
            <a href="{{ route('book.chapter', $next) }}">Chapter {{ $next->number }} &rarr;</a>
        @endif
    </span>
</nav>

@include('book.partials.chapter-sheet', ['chapter' => $chapter, 'page' => $page])

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
