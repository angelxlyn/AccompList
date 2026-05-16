<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

// Home / Dashboard
Route::get('/', [TaskController::class, 'home'])->name('home');

// Task list filtered by status (query: ?status=To+Do)
Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');

// Add Task form
Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');

// Store new task
Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');

// Edit Task form
Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');

// Update task
Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');

// Soft delete
Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

// Restore a trashed task
Route::patch('/tasks/{id}/restore', [TaskController::class, 'restore'])->name('tasks.restore');

// Permanently delete a trashed task
Route::delete('/tasks/{id}/force-delete', [TaskController::class, 'forceDelete'])->name('tasks.forceDelete');

// Manage Lists overview
Route::get('/manage', [TaskController::class, 'manageLists'])->name('tasks.manage');

// Bulk actions
Route::post('/tasks/bulk-destroy', [TaskController::class, 'bulkDestroy'])->name('tasks.bulkDestroy');
Route::post('/tasks/bulk-restore', [TaskController::class, 'bulkRestore'])->name('tasks.bulkRestore');
Route::post('/tasks/bulk-force-delete', [TaskController::class, 'bulkForceDelete'])->name('tasks.bulkForceDelete');
