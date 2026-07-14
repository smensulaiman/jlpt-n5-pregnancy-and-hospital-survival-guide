@extends('layouts.book')

@section('title', 'JLPT N5 Pregnancy & Hospital Survival Guide')

@section('content')

@include('book.partials.front-matter', ['tocBreak' => true])

@foreach($parts as $part)
    @foreach($part->chapters as $chapter)
        @include('book.partials.chapter-sheet', [
            'chapter' => $chapter,
            'page' => $pages[$chapter->number] ?? null,
            'break' => ! ($loop->parent->last && $loop->last),
        ])
    @endforeach
@endforeach

@endsection
