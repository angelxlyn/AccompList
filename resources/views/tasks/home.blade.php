@extends('tasks.layout')

@section('content')
    <div class="content-card">

        {{-- ── Top Bar: Title + Search + Add Task ── --}}
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">

            <h5 class="fw-bold mb-0">
                <i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard
            </h5>

            <div class="d-flex align-items-center gap-2 flex-wrap ms-auto">

                <div class="input-group" style="width:250px;">
                    <span class="input-group-text bg-transparent border-end-0 px-3">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" id="searchName" placeholder="Search tasks..."
                        class="form-control bg-transparent border-start-0 ps-0" style="box-shadow:none;">
                </div>

                <select id="filterPriority" class="form-select px-3" style="width:140px; box-shadow:none;">
                    <option value="">Priority</option>
                    <option value="High">High</option>
                    <option value="Medium">Medium</option>
                    <option value="Low">Low</option>
                </select>

                <select id="filterStatus" class="form-select px-3" style="width:145px; box-shadow:none;">
                    <option value="">Status</option>
                    <option value="To Do">To Do</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Completed">Completed</option>
                    <option value="Submitted">Submitted</option>
                </select>

                <button id="btnClear" class="btn btn-outline-secondary d-none px-3">
                    <i class="bi bi-x-lg me-1"></i>Clear
                </button>
            </div>

            <button type="button" class="btn btn-add-task" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                <i class="bi bi-plus-lg me-1"></i>Add Task
            </button>
        </div>

        {{-- ── Summary Count Cards ── --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-4 col-lg">
                <a href="{{ route('home') }}" class="text-decoration-none d-block h-100">
                    <div class="card text-center border-0 shadow-sm h-100 card-hover" style="background:#f5f3ff;">
                        <div class="card-body py-3">
                            <div style="font-size:2rem;font-weight:700;color:#7c3aed;">{{ $counts['total'] }}</div>
                            <div class="text-muted small fw-semibold">Total Tasks</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg">
                <a href="{{ route('tasks.index', ['status' => 'To Do']) }}" class="text-decoration-none d-block h-100">
                    <div class="card text-center border-0 shadow-sm h-100 card-hover" style="background:#eef2ff;">
                        <div class="card-body py-3">
                            <div style="font-size:2rem;font-weight:700;color:#4f46e5;">{{ $counts['todo'] }}</div>
                            <div class="text-muted small fw-semibold">To Do</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg">
                <a href="{{ route('tasks.index', ['status' => 'In Progress']) }}"
                    class="text-decoration-none d-block h-100">
                    <div class="card text-center border-0 shadow-sm h-100 card-hover" style="background:#fff7ed;">
                        <div class="card-body py-3">
                            <div style="font-size:2rem;font-weight:700;color:#ea580c;">{{ $counts['inprogress'] }}</div>
                            <div class="text-muted small fw-semibold">In Progress</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-6 col-lg">
                <a href="{{ route('tasks.index', ['status' => 'Completed']) }}" class="text-decoration-none d-block h-100">
                    <div class="card text-center border-0 shadow-sm h-100 card-hover" style="background:#f0fdf4;">
                        <div class="card-body py-3">
                            <div style="font-size:2rem;font-weight:700;color:#16a34a;">{{ $counts['completed'] }}</div>
                            <div class="text-muted small fw-semibold">Completed</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-6 col-lg">
                <a href="{{ route('tasks.index', ['status' => 'Submitted']) }}" class="text-decoration-none d-block h-100">
                    <div class="card text-center border-0 shadow-sm h-100 card-hover" style="background:#f0f9ff;">
                        <div class="card-body py-3">
                            <div style="font-size:2rem;font-weight:700;color:#0284c7;">{{ $counts['submitted'] }}</div>
                            <div class="text-muted small fw-semibold">Submitted</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- ── Task Table ── --}}
        @if($tasks->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-inbox display-4 text-muted"></i>
                <p class="mt-3 text-muted fs-5">
                    No tasks yet.
                    <a href="#" data-bs-toggle="modal" data-bs-target="#addTaskModal">Add your first task</a>.
                </p>
            </div>
        @else
            @php
                $hasDescription = $tasks->some(fn($t) => !empty($t->description));
            @endphp

            <table class="table task-table table-hover mb-0" id="taskTable">
                <thead>
                    <tr>
                        <th style="width:60px;">No.</th>
                        <th>Task Name</th>
                        @if($hasDescription)
                            <th>Description</th>
                        @endif
                        <th style="width:110px;">Priority</th>
                        <th style="width:130px;">Deadline</th>
                        <th style="width:120px;">Status</th>
                        <th style="width:100px; white-space:nowrap;" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="taskBody">
                    @foreach($tasks as $index => $task)
                        <tr data-name="{{ strtolower($task->task_name) }}" data-priority="{{ $task->priority }}"
                            data-status="{{ $task->status }}">

                            <td class="text-muted fw-semibold row-sn">
                                {{ ($tasks->currentPage() - 1) * $tasks->perPage() + $index + 1 }}.
                            </td>

                            <td>
                                {{ $task->task_name }}
                                @php
                                    $now = now()->startOfDay();
                                    $deadline = $task->deadline->startOfDay();
                                    $diff = $now->diffInDays($deadline, false);
                                @endphp
                                @if(!in_array($task->status, ['Completed', 'Submitted']))
                                    @if($diff < 0)
                                        <span class="badge bg-danger ms-1" style="font-size:.65rem;vertical-align:middle;">Overdue</span>
                                    @elseif($diff === 0)
                                        <span class="badge bg-warning text-dark ms-1" style="font-size:.65rem;vertical-align:middle;">Due
                                            Today</span>
                                    @elseif($diff <= 14)
                                        <span class="badge bg-info text-white ms-1" style="font-size:.65rem;vertical-align:middle;">
                                            {{ $diff === 1 ? 'Due Tomorrow' : "Due in {$diff} days" }}
                                        </span>
                                    @endif
                                @endif
                            </td>

                            @if($hasDescription)
                                <td class="small text-muted">{!! $task->linked_description !!}</td>
                            @endif

                            <td>
                                <span class="badge badge-{{ strtolower($task->priority) }} text-white px-2 py-1">
                                    {{ $task->priority }}
                                </span>
                            </td>

                            <td>{{ $task->deadline->format('Y-m-d') }}</td>

                            <td>
                                <span class="badge badge-{{ $task->status_color }} px-2 py-1">
                                    {{ $task->status }}
                                </span>
                            </td>

                            <td style="white-space:nowrap;" class="text-center">
                                {{-- Edit (opens modal) --}}
                                <button type="button" class="btn btn-update btn-sm btn-edit-task" title="Update Task"
                                    data-bs-toggle="modal" data-bs-target="#editTaskModal" data-id="{{ $task->id }}"
                                    data-name="{{ $task->task_name }}" data-priority="{{ $task->priority }}"
                                    data-deadline="{{ $task->deadline->format('Y-m-d') }}" data-status="{{ $task->status }}"
                                    data-description="{{ $task->description }}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                {{-- Soft Delete --}}
                                <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Move \'{{ addslashes($task->task_name) }}\' to trash?')">
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

            {{-- No-results row (shown by JS when filter finds nothing) --}}
            <div id="noResults" class="text-center py-4 d-none">
                <i class="bi bi-search display-5 text-muted"></i>
                <p class="mt-2 text-muted">No tasks match your search.</p>
            </div>

            {{-- ── Pagination + Count ── --}}
            <div class="d-flex align-items-center justify-content-between mt-3 flex-wrap gap-2">
                <p class="text-muted mb-0 small">
                    {{-- Shown while filtering (JS swaps these) --}}
                    <span id="filteredText" class="d-none">
                        Showing <strong id="visibleCount"></strong> matching task(s) on this page.
                    </span>
                    {{-- Shown normally --}}
                    <span id="paginationText">
                        @if($tasks->total() === 0)
                            No tasks found.
                        @elseif($tasks->total() <= $tasks->perPage())
                            Showing <strong>{{ $tasks->total() }}</strong> task(s).
                        @else
                            Showing
                            <strong>{{ ($tasks->currentPage() - 1) * $tasks->perPage() + 1 }}–{{ min($tasks->currentPage() * $tasks->perPage(), $tasks->total()) }}</strong>
                            of <strong>{{ $tasks->total() }}</strong> tasks.
                        @endif
                    </span>
                </p>

                @if($tasks->hasPages())
                    <nav>
                        <ul class="pagination mb-0">
                            <li class="page-item {{ $tasks->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $tasks->previousPageUrl() }}">&#8249;</a>
                            </li>
                            @foreach($tasks->getUrlRange(1, $tasks->lastPage()) as $page => $url)
                                <li class="page-item {{ $page == $tasks->currentPage() ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endforeach
                            <li class="page-item {{ !$tasks->hasMorePages() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $tasks->nextPageUrl() }}">&#8250;</a>
                            </li>
                        </ul>
                    </nav>
                @endif
            </div>
        @endif

    </div>

    <script>
        (function () {
            const nameInput = document.getElementById('searchName');
            const prioritySelect = document.getElementById('filterPriority');
            const statusSelect = document.getElementById('filterStatus');
            const clearBtn = document.getElementById('btnClear');
            const taskBody = document.getElementById('taskBody');
            const noResults = document.getElementById('noResults');
            const visibleCount = document.getElementById('visibleCount');
            const filteredText = document.getElementById('filteredText');
            const paginationText = document.getElementById('paginationText');

            if (!taskBody) return; // no tasks rendered — nothing to filter

            function applyFilters() {
                const name = nameInput.value.toLowerCase().trim();
                const priority = prioritySelect.value;
                const status = statusSelect.value;
                const isFiltering = name !== '' || priority !== '' || status !== '';

                const rows = taskBody.querySelectorAll('tr');
                let visible = 0;

                rows.forEach(row => {
                    const match =
                        row.dataset.name.includes(name) &&
                        (priority === '' || row.dataset.priority === priority) &&
                        (status === '' || row.dataset.status === status);

                    row.classList.toggle('d-none', !match);
                    if (match) visible++;
                });

                // Re-number visible rows sequentially
                let sn = 1;
                rows.forEach(row => {
                    if (!row.classList.contains('d-none')) {
                        row.querySelector('.row-sn').textContent = sn++ + '.';
                    }
                });

                // Toggle no-results message
                if (noResults) noResults.classList.toggle('d-none', visible > 0);

                // Swap pagination text / filter text
                if (filteredText && paginationText) {
                    filteredText.classList.toggle('d-none', !isFiltering);
                    paginationText.classList.toggle('d-none', isFiltering);
                }
                if (visibleCount) visibleCount.textContent = visible;

                // Show/hide Clear button
                clearBtn.classList.toggle('d-none', !isFiltering);
            }

            nameInput.addEventListener('input', applyFilters);
            prioritySelect.addEventListener('change', applyFilters);
            statusSelect.addEventListener('change', applyFilters);

            clearBtn.addEventListener('click', function () {
                nameInput.value = '';
                prioritySelect.value = '';
                statusSelect.value = '';
                applyFilters();
            });
        })();
    </script>
@endsection