<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display the Home dashboard with all tasks and count cards.
     */
    public function home()
    {
        $counts = [
            'todo'       => Task::byStatus('To Do')->count(),
            'inprogress' => Task::byStatus('In Progress')->count(),
            'completed'  => Task::byStatus('Completed')->count(),
            'submitted'  => Task::byStatus('Submitted')->count(),
            'total'      => Task::count(),
        ];

        $tasks = Task::orderBy('deadline', 'asc')->paginate(10);

        return view('tasks.home', compact('counts', 'tasks'));
    }

    /**
     * Display tasks filtered by status.
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'To Do');

        $tasks = Task::byStatus($status)
                     ->orderBy('deadline', 'asc')
                     ->paginate(10);

        return view('tasks.index', compact('tasks', 'status'));
    }

    /**
     * Show the form for creating a new task.
     */
    public function create()
    {
        return view('tasks.create');
    }

    /**
     * Store a newly created task in the database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'task_name'   => 'required|string|max:255',
            'priority'    => 'required|in:High,Medium,Low',
            'deadline'    => 'required|date',
            'status'      => 'required|in:To Do,In Progress,Completed,Submitted',
            'description' => 'nullable|string|max:1000',
        ]);

        Task::create($validated);

        return redirect()->back()
                         ->with('success', 'Task "' . $validated['task_name'] . '" added successfully!');
    }

    /**
     * Show the form for editing an existing task.
     */
    public function edit(Task $task)
    {
        return view('tasks.edit', compact('task'));
    }

    /**
     * Update the specified task in the database.
     */
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'task_name'   => 'required|string|max:255',
            'priority'    => 'required|in:High,Medium,Low',
            'deadline'    => 'required|date',
            'status'      => 'required|in:To Do,In Progress,Completed,Submitted',
            'description' => 'nullable|string|max:1000',
        ]);

        $task->update($validated);

        return redirect()->back()
                         ->with('success', 'Task "' . $task->task_name . '" updated successfully!');
    }

    /**
     * Soft-delete the specified task.
     */
    public function destroy(Task $task)
    {
        $name   = $task->task_name;
        $status = $task->status;
        $task->delete();

        return redirect()->back()
                         ->with('success', 'Task "' . $name . '" moved to trash.');
    }

    /**
     * Restore a soft-deleted task.
     */
    public function restore($id)
    {
        $task = Task::onlyTrashed()->findOrFail($id);
        $task->restore();

        return redirect()->back()
                         ->with('success', 'Task "' . $task->task_name . '" has been restored.');
    }

    /**
     * Permanently delete a soft-deleted task.
     */
    public function forceDelete($id)
    {
        $task = Task::onlyTrashed()->findOrFail($id);
        $name = $task->task_name;
        $task->forceDelete();

        return redirect()->back()
                         ->with('success', 'Task "' . $name . '" permanently deleted.');
    }

    /**
     * Manage list categories / overview page with dynamic filtering and trash.
     */
    public function manageLists(Request $request)
    {
        $category = $request->query('category', 'status'); // status, priority, trash
        $filter   = $request->query('filter');

        // Default filters if not provided
        if (!$filter && $category !== 'trash') {
            $filter = ($category === 'status') ? 'To Do' : 'High';
        }

        $tasks = collect();
        if ($category === 'status') {
            $tasks = Task::where('status', $filter)->orderBy('deadline', 'asc')->paginate(10);
        } elseif ($category === 'priority') {
            $tasks = Task::where('priority', $filter)->orderBy('deadline', 'asc')->paginate(10);
        } elseif ($category === 'trash') {
            $tasks = Task::onlyTrashed()->orderBy('deleted_at', 'desc')->paginate(10);
        }

        return view('tasks.manage', compact('tasks', 'category', 'filter'));
    }

    /**
     * Bulk soft-delete tasks.
     */
    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!empty($ids)) {
            Task::whereIn('id', $ids)->delete();
            return redirect()->back()->with('success', count($ids) . ' tasks moved to trash.');
        }
        return redirect()->back()->with('error', 'No tasks selected.');
    }

    /**
     * Bulk restore tasks.
     */
    public function bulkRestore(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!empty($ids)) {
            Task::onlyTrashed()->whereIn('id', $ids)->restore();
            return redirect()->back()->with('success', count($ids) . ' tasks restored.');
        }
        return redirect()->back()->with('error', 'No tasks selected.');
    }

    /**
     * Bulk permanent delete.
     */
    public function bulkForceDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!empty($ids)) {
            Task::onlyTrashed()->whereIn('id', $ids)->forceDelete();
            return redirect()->back()->with('success', count($ids) . ' tasks permanently deleted.');
        }
        return redirect()->back()->with('error', 'No tasks selected.');
    }
}
