@extends('tasks.layout')

@section('content')
    <div class="content-card">

        {{-- ── Header: Title + Add Task ── --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h5 class="fw-bold mb-0">
                <i class="bi bi-gear me-2 text-secondary"></i>Manage Lists
            </h5>
            <button type="button" class="btn btn-add-task" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                <i class="bi bi-plus-lg me-1"></i>Add Task
            </button>
        </div>

        {{-- ── Primary Category Tabs ── --}}
        <div class="manage-pills-container mb-4">
            <ul class="nav nav-pills manage-pills bg-light p-1 rounded-3 d-inline-flex" style="border:1px solid #dee2e6;">
                <li class="nav-item">
                    <a class="nav-link {{ $category === 'status' ? 'active' : '' }}"
                        href="{{ route('tasks.manage', ['category' => 'status']) }}">
                        <i class="bi bi-grid-3x3-gap me-1"></i>By Status
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $category === 'priority' ? 'active' : '' }}"
                        href="{{ route('tasks.manage', ['category' => 'priority']) }}">
                        <i class="bi bi-flag me-1"></i>By Priority
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $category === 'trash' ? 'active' : '' }}"
                        href="{{ route('tasks.manage', ['category' => 'trash']) }}">
                        <i class="bi bi-trash3 me-1"></i>Trash
                    </a>
                </li>
            </ul>
        </div>

        {{-- ── Secondary Filter Pills ── --}}
        @if($category === 'status')
            <div class="filter-pills-container d-flex gap-2 mb-4">
                @foreach(\App\Models\Task::STATUSES as $s)
                    <a href="{{ route('tasks.manage', ['category' => 'status', 'filter' => $s]) }}"
                        class="btn btn-sm {{ $filter === $s ? 'btn-primary' : 'btn-outline-secondary' }} px-3 rounded-pill fw-semibold">
                        {{ $s }}
                    </a>
                @endforeach
            </div>
        @elseif($category === 'priority')
            <div class="filter-pills-container d-flex gap-2 mb-4">
                @foreach(\App\Models\Task::PRIORITIES as $p)
                    <a href="{{ route('tasks.manage', ['category' => 'priority', 'filter' => $p]) }}"
                        class="btn btn-sm {{ $filter === $p ? 'btn-primary' : 'btn-outline-secondary' }} px-3 rounded-pill fw-semibold">
                        {{ $p }}
                    </a>
                @endforeach
            </div>
        @endif

        {{-- ── Task Table ── --}}
        <div class="table-responsive">
            @if($tasks->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-4 text-muted opacity-50"></i>
                    <p class="text-muted mt-3 mb-0">No tasks found in this selection.</p>
                    <small class="text-muted">Try choosing a different category or filter.</small>
                </div>
            @else
                @php
                    $hasDescription = $tasks->some(fn($t) => !empty($t->description));
                @endphp

                <table class="table task-table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width:40px;">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th style="width:60px;" class="col-no">No.</th>
                            <th>Task Name</th>
                            @if($hasDescription)
                                <th>Description</th>
                            @endif
                            @if($category !== 'priority')
                                <th style="width:120px;">Priority</th>
                            @endif
                            <th style="width:150px; white-space:nowrap;">Deadline</th>
                            @if($category === 'trash')
                                <th style="width:150px; white-space:nowrap;">Deleted At</th>
                            @elseif($category !== 'status')
                                <th style="width:140px;">Status</th>
                            @endif
                            <th class="text-end" style="width:130px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tasks as $index => $task)
                            <tr class="{{ $category === 'trash' ? 'text-muted' : '' }}">

                                {{-- Checkbox --}}
                                <td>
                                    <input type="checkbox" class="task-checkbox form-check-input" value="{{ $task->id }}">
                                </td>

                                {{-- Row number --}}
                                <td class="col-no">{{ $tasks->firstItem() + $index }}.</td>

                                {{-- Task Name + deadline badge --}}
                                <td class="fw-medium">
                                    <span>
                                        {{ $task->task_name }}
                                    </span>
                                    @if($category !== 'trash' && !in_array($task->status, ['Completed', 'Submitted']))
                                        @php
                                            $now = now()->startOfDay();
                                            $deadline = $task->deadline->startOfDay();
                                            $diff = $now->diffInDays($deadline, false);
                                        @endphp
                                        @if($diff < 0)
                                            <span class="badge bg-danger ms-1"
                                                style="font-size:.65rem;vertical-align:middle;">Overdue</span>
                                        @elseif($diff >= 0 && $diff <= 3)
                                            <span class="badge bg-warning text-dark ms-1" style="font-size:.65rem;vertical-align:middle;">
                                                @if($diff == 0) Due Today
                                                @elseif($diff == 1) Due Tomorrow
                                                @else Due in {{ $diff }} days
                                                @endif
                                            </span>
                                        @elseif($diff > 3 && $diff <= 14)
                                            <span class="badge bg-info text-white ms-1" style="font-size:.65rem;vertical-align:middle;">
                                                Due in {{ $diff }} days
                                            </span>
                                        @endif
                                    @endif
                                </td>

                                {{-- Description (only if any task on page has one) --}}
                                @if($hasDescription)
                                    <td class="small text-muted">{!! $task->linked_description !!}</td>
                                @endif

                                {{-- Priority (hidden when filtering by priority — already obvious) --}}
                                @if($category !== 'priority')
                                    <td>
                                        <span class="badge badge-{{ strtolower($task->priority) }} text-white px-2 py-1">
                                            {{ $task->priority }}
                                        </span>
                                    </td>
                                @endif

                                {{-- Deadline --}}
                                <td style="white-space:nowrap;">{{ $task->deadline->format('Y-m-d') }}</td>

                                {{-- Deleted At / Status --}}
                                @if($category === 'trash')
                                    <td style="white-space:nowrap;">
                                        {{ $task->deleted_at->format('Y-m-d') }}<br>
                                        {{ $task->deleted_at->format('H:i') }}
                                    </td>
                                @elseif($category !== 'status')
                                    <td>
                                        <span class="badge badge-{{ $task->status_color }} px-2 py-1"
                                            style="font-size:.75rem;font-weight:600;">
                                            {{ $task->status }}
                                        </span>
                                    </td>
                                @endif

                                {{-- Actions --}}
                                <td class="text-end" style="white-space:nowrap;">
                                    @if($category === 'trash')
                                        {{-- Restore --}}
                                        <form action="{{ route('tasks.restore', $task->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-update" title="Restore Task">
                                                <i class="bi bi-arrow-counterclockwise"></i>
                                            </button>
                                        </form>
                                        {{-- Delete Forever --}}
                                        <form action="{{ route('tasks.forceDelete', $task->id) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('Permanently delete \'{{ addslashes($task->task_name) }}\'? This cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-remove" title="Delete Permanently">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    @else
                                        {{-- Edit (opens modal) --}}
                                        <button type="button" class="btn btn-sm btn-update btn-edit-task" title="Edit Task"
                                            data-bs-toggle="modal" data-bs-target="#editTaskModal" data-id="{{ $task->id }}"
                                            data-name="{{ $task->task_name }}" data-priority="{{ $task->priority }}"
                                            data-deadline="{{ $task->deadline->format('Y-m-d') }}" data-status="{{ $task->status }}"
                                            data-description="{{ $task->description }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        {{-- Soft Delete --}}
                                        <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-remove" title="Move to Trash"
                                                onclick="return confirm('Move \'{{ addslashes($task->task_name) }}\' to trash?')">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- ── Pagination + Count ── --}}
        <div class="d-flex align-items-center justify-content-between mt-4 flex-wrap gap-2">
            <p class="text-muted mb-0 small">
                @if($tasks->total() === 0)
                    No records found.
                @elseif($tasks->total() <= $tasks->perPage())
                    Showing <strong>{{ $tasks->total() }}</strong> task(s).
                @else
                    Showing
                    <strong>{{ $tasks->firstItem() }}–{{ $tasks->lastItem() }}</strong>
                    of <strong>{{ $tasks->total() }}</strong> tasks.
                @endif
            </p>
            @if($tasks->hasPages())
                <nav>
                    <ul class="pagination mb-0">
                        <li class="page-item {{ $tasks->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $tasks->appends(request()->query())->previousPageUrl() }}">&#8249;</a>
                        </li>
                        @foreach($tasks->getUrlRange(1, $tasks->lastPage()) as $page => $url)
                            <li class="page-item {{ $page == $tasks->currentPage() ? 'active' : '' }}">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endforeach
                        <li class="page-item {{ !$tasks->hasMorePages() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $tasks->appends(request()->query())->nextPageUrl() }}">&#8250;</a>
                        </li>
                    </ul>
                </nav>
            @endif
        </div>

        {{-- ── Bulk Action Floating Bar ── --}}
        <div id="bulkActionBar" class="bulk-action-bar shadow-lg border d-none">
            <div class="d-flex align-items-center justify-content-between px-3 py-2">
                <div class="text-white small fw-bold me-4">
                    <span id="selectedCount">0</span> item(s) selected
                </div>
                <div class="d-flex gap-2">
                    @if($category === 'trash')
                        <button type="button" class="btn btn-sm btn-light fw-bold"
                            onclick="submitBulk('{{ route('tasks.bulkRestore') }}')">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Restore
                        </button>
                        <button type="button" class="btn btn-sm btn-danger fw-bold"
                            onclick="submitBulk('{{ route('tasks.bulkForceDelete') }}', true)">
                            <i class="bi bi-trash3 me-1"></i>Delete Permanently
                        </button>
                    @else
                        <button type="button" class="btn btn-sm btn-danger fw-bold"
                            onclick="submitBulk('{{ route('tasks.bulkDestroy') }}')">
                            <i class="bi bi-trash3 me-1"></i>Move to Trash
                        </button>
                    @endif
                    <button type="button" class="btn btn-sm btn-outline-light border-0 ms-2" onclick="deselectAll()">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Hidden form used by bulk actions --}}
        <form id="bulkForm" method="POST" class="d-none">
            @csrf
            <div id="bulkIdsContainer"></div>
        </form>

    </div>

    <style>
        .manage-pills {
            gap: 4px;
        }

        .manage-pills .nav-link {
            color: #64748b;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .manage-pills .nav-link:hover {
            background-color: rgba(0, 0, 0, 0.05);
            color: #334155;
        }

        .manage-pills .nav-link.active {
            background-color: #fff !important;
            color: #0d6efd !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        [data-bs-theme="dark"] .manage-pills {
            background-color: #18181b !important;
            border-color: #3f3f46 !important;
        }

        [data-bs-theme="dark"] .manage-pills .nav-link {
            color: #a1a1aa;
        }

        [data-bs-theme="dark"] .manage-pills .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.05);
            color: #f4f4f5;
        }

        [data-bs-theme="dark"] .manage-pills .nav-link.active {
            background-color: #27272a !important;
            color: #3b82f6 !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }

        .bulk-action-bar {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            background: #1a1a2e;
            border-radius: 12px;
            min-width: 320px;
            z-index: 1050;
            padding: 6px;
            animation: bulkSlideUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes bulkSlideUp {
            from {
                transform: translate(-50%, 100%);
                opacity: 0;
            }

            to {
                transform: translate(-50%, 0);
                opacity: 1;
            }
        }

        [data-bs-theme="dark"] .bulk-action-bar {
            background: #27272a;
            border-color: #3f3f46 !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.task-checkbox');
            const bulkBar = document.getElementById('bulkActionBar');
            const selectedCount = document.getElementById('selectedCount');
            const bulkIdsContainer = document.getElementById('bulkIdsContainer');
            const bulkForm = document.getElementById('bulkForm');

            function updateBulkBar() {
                const checkedCount = document.querySelectorAll('.task-checkbox:checked').length;
                selectedCount.textContent = checkedCount;
                bulkBar.classList.toggle('d-none', checkedCount === 0);

                if (selectAll) {
                    selectAll.checked = checkedCount === checkboxes.length && checkboxes.length > 0;
                    selectAll.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;
                }
            }

            if (selectAll) {
                selectAll.addEventListener('change', function () {
                    checkboxes.forEach(cb => cb.checked = selectAll.checked);
                    updateBulkBar();
                });
            }

            checkboxes.forEach(cb => cb.addEventListener('change', updateBulkBar));

            window.deselectAll = function () {
                checkboxes.forEach(cb => cb.checked = false);
                if (selectAll) selectAll.checked = false;
                updateBulkBar();
            };

            window.submitBulk = function (url, confirmFirst = false) {
                if (confirmFirst && !confirm('Are you sure you want to permanently delete the selected items? This cannot be undone.')) {
                    return;
                }

                bulkIdsContainer.innerHTML = '';
                document.querySelectorAll('.task-checkbox:checked').forEach(cb => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = cb.value;
                    bulkIdsContainer.appendChild(input);
                });

                bulkForm.action = url;
                bulkForm.submit();
            };
        });
    </script>
@endsection