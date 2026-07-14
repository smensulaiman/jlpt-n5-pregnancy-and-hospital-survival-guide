@extends('layouts.book')

@section('title', 'JLPT N5 Pregnancy & Hospital Survival Guide')

@section('content')

<nav class="chrome" aria-label="Book navigation">
    <span class="chrome-group">
        <span style="padding: 5px 0;">実用日本語シリーズ</span>
    </span>
    <span class="chrome-label">JLPT N5 Pregnancy &amp; Hospital Survival Guide</span>
    <span class="chrome-group">
        <a class="pdf-btn" href="{{ route('book.pdf') }}">Download PDF &middot; PDF保存</a>
        <button type="button" class="print-btn" onclick="window.print()">Print &middot; 印刷</button>
        @if($parts->isNotEmpty() && $parts->first()->chapters->isNotEmpty())
            <a href="{{ route('book.chapter', $parts->first()->chapters->first()) }}">Start reading &rarr;</a>
        @endif
    </span>
</nav>

@include('book.partials.front-matter')

<nav class="chrome chrome--bottom" aria-label="Book navigation">
    <span class="chrome-label">First Edition &middot; Twenty Chapters &middot; Print-Ready A4</span>
</nav>

@endsection
