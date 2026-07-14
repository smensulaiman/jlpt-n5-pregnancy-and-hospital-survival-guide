<?php

use App\Http\Controllers\BookController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BookController::class, 'index'])->name('book.index');
Route::get('/chapter/{chapter:slug}', [BookController::class, 'chapter'])->name('book.chapter');
