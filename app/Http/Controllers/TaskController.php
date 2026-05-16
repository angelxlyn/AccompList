<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    // ── Dashboard ─────────────────────────────────────────────────────────────

    /**
     * Home dashboard: summary counts + full paginated task list.
     */
    public function home()
    {
        $counts = [
            'todo' => Task::byStatus('To Do')->count(),
            'inprogress' => Task::byStatus('In Progress')->count(),
            'completed' => Task::byStatus('Completed')->count(),
            'submitted' => Task::byStatus('Submitted')->count(),
            'total' => Task::count(),
        ];

        $tasks = Task::orderBy('deadline', 'asc')->paginate(10);

        return view('tasks.home', compact('counts', 'tasks'));
    }

    // ── Status Tab Views ──────────────────────────────────────────────────────

    /**
     * Task list filtered by status (To Do / In Progress / Completed / Submitted).
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'To Do');

        $tasks = Task::byStatus($status)
            ->orderBy('deadline', 'asc')
            ->paginate(10);

        return view('tasks.index', compact('tasks', 'status'));
    }

    // ── CRUD ──────────────────────────────────────────────────────────────────

    /**
     * Show the standalone create form (fallback if modal is unavailable).
     */
    public function create()
    {
        return view('tasks.create');
    }

    /**
     * Store a newly created task.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'task_name' => 'required|string|max:255',
            'priority' => ['required', Rule::in(Task::PRIORITIES)],
            'deadline' => 'required|date',
            'status' => ['required', Rule::in(Task::STATUSES)],
            'description' => 'nullable|string|max:1000',
        ]);

        Task::create($validated);

        return redirect()->route('home')
            ->with('success', 'Task "' . $validated['task_name'] . '" added successfully!');
    }

    /**
     * Show the standalone edit form (fallback if modal is unavailable).
     */
    public function edit(Task $task)
    {
        return view('tasks.edit', compact('task'));
    }

    /**
     * Update an existing task.
     */
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'task_name' => 'required|string|max:255',
            'priority' => ['required', Rule::in(Task::PRIORITIES)],
            'deadline' => 'required|date',
            'status' => ['required', Rule::in(Task::STATUSES)],
            'description' => 'nullable|string|max:1000',
        ]);

        $task->update($validated);

        return redirect()->route('home')
            ->with('success', 'Task "' . $task->task_name . '" updated successfully!');
    }

    // ── Soft Delete / Restore / Force Delete ──────────────────────────────────

    /**
     * Soft-delete a task (sets deleted_at; excluded from normal queries).
     */
    public function destroy(Task $task)
    {
        $name = $task->task_name;
        $task->delete();

        return redirect()->route('home')
            ->with('success', 'Task "' . $name . '" moved to trash.');
    }

    /**
     * Restore a soft-deleted task.
     */
    public function restore($id)
    {
        $task = Task::onlyTrashed()->findOrFail($id);
        $task->restore();

        return redirect()->route('tasks.manage', ['category' => 'trash'])
            ->with('success', 'Task "' . $task->task_name . '" restored.');
    }

    /**
     * Permanently delete a soft-deleted task.
     */
    public function forceDelete($id)
    {
        $task = Task::onlyTrashed()->findOrFail($id);
        $name = $task->task_name;
        $task->forceDelete();

        return redirect()->route('tasks.manage', ['category' => 'trash'])
            ->with('success', 'Task "' . $name . '" permanently deleted.');
    }

    // ── Manage Lists ──────────────────────────────────────────────────────────

    /**
     * Manage hub: browse tasks by status, priority, or view the trash.
     */
    public function manageLists(Request $request)
    {
        $category = $request->query('category', 'status');
        $filter = $request->query('filter');

        // Default filter per category
        if (!$filter && $category !== 'trash') {
            $filter = ($category === 'priority') ? 'High' : 'To Do';
        }

        // Always return a LengthAwarePaginator so the blade never crashes
        // calling ->total(), ->firstItem(), etc.
        $tasks = match ($category) {
            'status' => Task::where('status', $filter)->orderBy('deadline', 'asc')->paginate(10),
            'priority' => Task::where('priority', $filter)->orderBy('deadline', 'asc')->paginate(10),
            'trash' => Task::onlyTrashed()->orderBy('deleted_at', 'desc')->paginate(10),
            default => Task::whereRaw('0 = 1')->paginate(10), // safe empty paginator
        };

        return view('tasks.manage', compact('tasks', 'category', 'filter'));
    }

    // ── Bulk Actions ──────────────────────────────────────────────────────────

    /**
     * Bulk soft-delete selected tasks.
     */
    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return redirect()->route('tasks.manage')->with('error', 'No tasks selected.');
        }

        Task::whereIn('id', $ids)->delete();

        return redirect()->route('tasks.manage')
            ->with('success', count($ids) . ' task(s) moved to trash.');
    }

    /**
     * Bulk restore selected trashed tasks.
     */
    public function bulkRestore(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return redirect()->route('tasks.manage', ['category' => 'trash'])
                ->with('error', 'No tasks selected.');
        }

        Task::onlyTrashed()->whereIn('id', $ids)->restore();

        return redirect()->route('tasks.manage', ['category' => 'trash'])
            ->with('success', count($ids) . ' task(s) restored.');
    }

    /**
     * Bulk permanently delete selected trashed tasks.
     */
    public function bulkForceDelete(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return redirect()->route('tasks.manage', ['category' => 'trash'])
                ->with('error', 'No tasks selected.');
        }

        Task::onlyTrashed()->whereIn('id', $ids)->forceDelete();

        return redirect()->route('tasks.manage', ['category' => 'trash'])
            ->with('success', count($ids) . ' task(s) permanently deleted.');
    }
}