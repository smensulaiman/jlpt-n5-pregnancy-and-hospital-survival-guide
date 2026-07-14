<?php

use App\Http\Controllers\BookController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BookController::class, 'index'])->name('book.index');
Route::get('/print', [BookController::class, 'print'])->name('book.print');
Route::get('/download/pdf', [BookController::class, 'pdf'])->name('book.pdf');
Route::get('/chapter/{chapter:slug}', [BookController::class, 'chapter'])->name('book.chapter');
