<?php

use App\Http\Controllers\CalculationHistoryController;
use App\Http\Controllers\CalculatorController;
use App\Http\Controllers\DimensionOptionController;
use App\Http\Controllers\TemplateController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CalculatorController::class, 'index'])->name('calculator.index');
Route::post('/calculate', [CalculatorController::class, 'calculate'])->name('calculator.calculate');

Route::resource('templates', TemplateController::class)->except(['show']);
Route::get('/history', [CalculationHistoryController::class, 'index'])->name('history.index');

foreach (['width', 'height', 'paper_size'] as $type) {
    Route::prefix($type.'-options')->name($type.'-options.')->group(function () use ($type) {
        Route::get('/', [DimensionOptionController::class, 'index'])->defaults('type', $type)->name('index');
        Route::get('/create', [DimensionOptionController::class, 'create'])->defaults('type', $type)->name('create');
        Route::post('/', [DimensionOptionController::class, 'store'])->defaults('type', $type)->name('store');
        Route::get('/{option}/edit', [DimensionOptionController::class, 'edit'])->defaults('type', $type)->name('edit');
        Route::put('/{option}', [DimensionOptionController::class, 'update'])->defaults('type', $type)->name('update');
        Route::delete('/{option}', [DimensionOptionController::class, 'destroy'])->defaults('type', $type)->name('destroy');
    });
}
