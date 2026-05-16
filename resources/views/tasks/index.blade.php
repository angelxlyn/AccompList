@extends('tasks.layout')

@section('content')
<div class="content-card">

    {{-- ── Header Section ── --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h5 class="fw-bold mb-0">
            @php
                $statusIcon = match($status) {
                    'To Do'       => 'bi-list-check text-primary',
                    'In Progress' => 'bi-arrow-repeat text-warning',
                    'Completed'   => 'bi-check2-circle text-success',
                    'Submitted'   => 'bi-send-check text-info',
                    default       => 'bi-list text-secondary'
                };
            @endphp
            <i class="bi {{ $statusIcon }} me-2"></i>{{ $status }}
        </h5>
        <button type="button" class="btn btn-add-task" data-bs-toggle="modal" data-bs-target="#addTaskModal">
            <i class="bi bi-plus-lg me-1"></i>Add Task
        </button>
    </div>

    {{-- ── Task Table ── --}}
    @if($tasks->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-inbox display-4 text-muted"></i>
            <p class="mt-3 text-muted fs-5">No tasks in <strong>{{ $status }}</strong>.</p>
            <button type="button" class="btn btn-add-task mt-2" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                <i class="bi bi-plus-lg me-1"></i>Add Your First Task
            </button>
        </div>
    @else
        @php
            $hasDescription = $tasks->some(fn($t) => !empty($t->description));
        @endphp
        <table class="table task-table table-hover mb-0">
            <thead>
                <tr>
                    <th style="width:60px;">No.</th>
                    <th>Task Name</th>
                    @if($hasDescription)
                        <th>Description</th>
                    @endif
                    <th style="width:110px;">Priority</th>
                    <th style="width:130px;">Deadline</th>
                    <th style="width:130px; white-space:nowrap;" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tasks as $index => $task)
                    <tr>
                        <td class="text-muted fw-semibold">
                            {{ ($tasks->currentPage() - 1) * $tasks->perPage() + $index + 1 }}.
                        </td>

                        <td>
                            {{ $task->task_name }}
                            @php
                                $now = now()->startOfDay();
                                $deadline = $task->deadline->startOfDay();
                                $diff = $now->diffInDays($deadline, false);
                            @endphp
                            @if($task->status !== 'Completed' && $task->status !== 'Submitted')
                                @if($diff < 0)
                                    <span class="badge bg-danger ms-1" style="font-size: 0.65rem; vertical-align: middle;">Overdue</span>
                                @elseif($diff == 0)
                                    <span class="badge bg-warning text-dark ms-1" style="font-size: 0.65rem; vertical-align: middle;">Due Today</span>
                                @elseif($diff > 0 && $diff <= 14)
                                    <span class="badge bg-info text-white ms-1" style="font-size: 0.65rem; vertical-align: middle;">
                                        {{ $diff == 1 ? 'Due Tomorrow' : "Due in $diff days" }}
                                    </span>
                                @endif
                            @endif
                        </td>

                        @if($hasDescription)
                            <td class="small text-muted">
                                {!! preg_replace('~(https?://[^\s<>]+)~', '<a href="$1" target="_blank" class="text-decoration-none">$1</a>', e($task->description)) !!}
                            </td>
                        @endif

                        <td>
                            <span class="badge badge-{{ strtolower($task->priority) }} text-white px-2 py-1">
                                {{ $task->priority }}
                            </span>
                        </td>

                        <td>{{ $task->deadline->format('Y-m-d') }}</td>

                        <td style="white-space:nowrap;" class="text-center">
                            <button type="button" 
                                    class="btn btn-update btn-sm btn-edit-task"
                                    title="Update Task"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editTaskModal"
                                    data-id="{{ $task->id }}"
                                    data-name="{{ $task->task_name }}"
                                    data-priority="{{ $task->priority }}"
                                    data-deadline="{{ $task->deadline->format('Y-m-d') }}"
                                    data-status="{{ $task->status }}"
                                    data-description="{{ $task->description }}">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <form action="{{ route('tasks.destroy', $task->id) }}"
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Remove task \'{{ addslashes($task->task_name) }}\'?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-remove btn-sm" title="Move to Trash">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-flex align-items-center justify-content-between mt-3 flex-wrap gap-2">
            <p class="text-muted mb-0 small">
                @if($tasks->total() === 0)
                    No tasks found.
                @elseif($tasks->total() === 1)
                    Showing <strong>1</strong> task.
                @elseif($tasks->total() <= $tasks->perPage())
                    Showing <strong>{{ $tasks->total() }}</strong> tasks.
                @else
                    Showing <strong>{{ ($tasks->currentPage() - 1) * $tasks->perPage() + 1 }}-{{ min($tasks->currentPage() * $tasks->perPage(), $tasks->total()) }}</strong> of <strong>{{ $tasks->total() }}</strong> tasks.
                @endif
            </p>
            @if($tasks->hasPages())
                <nav>
                    <ul class="pagination mb-0">
                        {{-- Previous --}}
                        <li class="page-item {{ $tasks->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $tasks->previousPageUrl() }}">&#8249;</a>
                        </li>

                        {{-- Page numbers --}}
                        @foreach($tasks->getUrlRange(1, $tasks->lastPage()) as $page => $url)
                            <li class="page-item {{ $page == $tasks->currentPage() ? 'active' : '' }}">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endforeach

                        {{-- Next --}}
                        <li class="page-item {{ !$tasks->hasMorePages() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $tasks->nextPageUrl() }}">&#8250;</a>
                        </li>
                    </ul>
                </nav>
            @endif
        </div>
    @endif
</div>
@endsection
