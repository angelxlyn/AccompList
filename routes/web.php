<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

// ── Home / Dashboard ──────────────────────────────────────────────────────────
Route::get('/', [TaskController::class, 'home'])->name('home');

// ── Manage Lists ──────────────────────────────────────────────────────────────
Route::get('/manage', [TaskController::class, 'manageLists'])->name('tasks.manage');

// ── Bulk Actions ──────────────────────────────────────────────────────────────
// IMPORTANT: These must be defined BEFORE the {task} wildcard routes below,
// otherwise Laravel will try to resolve "bulk-destroy" as a task ID and fail.
Route::post('/tasks/bulk-destroy', [TaskController::class, 'bulkDestroy'])->name('tasks.bulkDestroy');
Route::post('/tasks/bulk-restore', [TaskController::class, 'bulkRestore'])->name('tasks.bulkRestore');
Route::post('/tasks/bulk-force-delete', [TaskController::class, 'bulkForceDelete'])->name('tasks.bulkForceDelete');

// ── Task List (filtered by status) ───────────────────────────────────────────
Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');

// ── Create Task ───────────────────────────────────────────────────────────────
// Must also be before {task} wildcard so /tasks/create isn't treated as an ID.
Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');

// ── Restore / Force Delete (trashed tasks, ID-based) ─────────────────────────
// These use plain $id (not model binding) because soft-deleted records are
// excluded from the default binding scope.
Route::patch('/tasks/{id}/restore', [TaskController::class, 'restore'])->name('tasks.restore');
Route::delete('/tasks/{id}/force-delete', [TaskController::class, 'forceDelete'])->name('tasks.forceDelete');

// ── CRUD on active tasks (wildcard — must come last) ─────────────────────────
Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');