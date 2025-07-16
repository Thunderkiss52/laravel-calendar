<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Thunderkiss52\LaravelCalendar\Controllers\CalendarController;
use Thunderkiss52\LaravelCalendar\Controllers\TaskController;
Route::middleware(['web', 'auth'])->controller(CalendarController::class)->name('calendar.')->prefix('calendar')->group(function () {
    Route::get('/month/{year}/{month}', 'month')->name('month');
    Route::get('/day/{date}', 'day')->name('day');
    Route::get('/week/{date}', 'week')->name('week');
    Route::resource('task', TaskController::class);
    Route::get('/task/{task}/finish', [TaskController::class, 'markFinished'])->name('task.finish');
    Route::get('/user/{user}/tasks', [TaskController::class, 'byUser'])->name('user.tasks');
    Route::get('/tasks', fn() => Redirect::route('calendar.user.tasks', Auth::id()))->name('tasks');
});