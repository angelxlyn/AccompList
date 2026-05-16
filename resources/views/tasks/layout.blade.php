<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AccompList</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/AccompList.png') }}">
    <!-- Dark Mode Init -->
    <script>
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.setAttribute('data-bs-theme', 'dark');
        }
    </script>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f6fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* ── Top Navigation Tabs ── */
        .nav-tabs-custom {
            background: #ffffff;
            border-bottom: 2px solid #dee2e6;
            padding: 6px 1rem;
            display: flex;
            align-items: center;
        }

        .nav-brand {
            text-decoration: none;
            font-size: 1.1rem;
            font-weight: 700;
            color: #1a1a2e;
            letter-spacing: .5px;
            white-space: nowrap;
            padding: 0 1rem 0 .25rem;
            margin-right: .5rem;
            border-right: 2px solid #dee2e6;
            display: flex;
            align-items: center;
            gap: .4rem;
            transition: color .2s;
        }

        .nav-brand:hover,
        .nav-brand.active-brand {
            color: #0d6efd;
        }

        .nav-brand img {
            height: 30px;
            width: auto;
            object-fit: contain;
        }

        .nav-tabs-custom .nav-link {
            border: 1px solid transparent;
            border-radius: 0;
            padding: 12px 20px;
            font-weight: 600;
            color: #495057;
            border-bottom: none;
        }

        .nav-tabs-custom .nav-link:hover {
            background: #e9ecef;
            color: #212529;
        }

        .nav-tabs-custom .nav-link.active {
            background: #ffffff;
            color: #212529;
            border-color: #dee2e6 #dee2e6 #ffffff;
            border-top: 3px solid #0d6efd;
        }

        /* ── Main content card ── */
        .content-card {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .08);
            padding: 24px;
            margin-top: 20px;
        }

        .card-hover {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
        }

        /* ── Task Table ── */
        .task-table th {
            font-weight: 700;
            color: #212529;
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            padding: 12px 16px;
        }

        .task-table td {
            vertical-align: middle;
            padding: 12px 16px;
        }

        .task-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* ── Buttons ── */
        .btn-add-task {
            background: linear-gradient(135deg, #4f46e5, #3730a3);
            color: #fff;
            font-weight: 600;
            padding: 8px 20px;
            border-radius: 6px;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-add-task:hover {
            background: linear-gradient(135deg, #4338ca, #312e81);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .btn-update {
            background-color: #198754;
            color: #fff;
            font-size: .85rem;
            padding: 5px 14px;
        }

        .btn-update:hover {
            background-color: #157347;
            color: #fff;
        }

        .btn-remove {
            background-color: #dc3545;
            color: #fff;
            font-size: .85rem;
            padding: 5px 14px;
        }

        .btn-remove:hover {
            background-color: #b02a37;
            color: #fff;
        }

        /* ── Priority badges ── */
        .badge-high {
            background: #dc3545;
        }

        .badge-medium {
            background: #fd7e14;
        }

        .badge-low {
            background: #198754;
        }

        /* ── Status badges (matches summary cards exactly) ── */
        .badge-todo {
            background: #4f46e5;
            color: #fff;
        }

        .badge-inprogress {
            background: #ea580c;
            color: #fff;
        }

        .badge-completed {
            background: #16a34a;
            color: #fff;
        }

        .badge-submitted {
            background: #0284c7;
            color: #fff;
        }

        /* ── Flash alert ── */
        .alert-flash {
            border-radius: 6px;
        }

        /* ── Pagination ── */
        .pagination {
            margin: 0;
        }

        .page-link {
            color: #1a1a2e;
            border: 1px solid #dee2e6;
            padding: 6px 13px;
            font-size: .875rem;
        }

        .page-link:hover {
            background-color: #e9ecef;
            color: #1a1a2e;
            border-color: #dee2e6;
        }

        .page-item.active .page-link {
            background-color: #1a1a2e;
            border-color: #1a1a2e;
            color: #fff;
        }

        .page-item.disabled .page-link {
            color: #adb5bd;
            background-color: #fff;
        }

        /* ── Add Task Modal ── */
        .modal-header-custom {
            background: #1a1a2e;
            color: #fff;
            border-bottom: none;
            border-radius: 8px 8px 0 0;
        }

        .modal-header-custom .btn-close {
            filter: invert(1) grayscale(1);
        }

        .modal-content {
            border-radius: 10px;
            border: none;
            box-shadow: 0 8px 32px rgba(0, 0, 0, .18);
        }

        /* ── Dark Mode Overrides ── */
        [data-bs-theme="dark"] body {
            background-color: #18181b;
        }
        [data-bs-theme="dark"] .nav-tabs-custom,
        [data-bs-theme="dark"] .content-card {
            background: #27272a;
        }
        [data-bs-theme="dark"] .nav-tabs-custom {
            border-bottom-color: #3f3f46;
        }
        [data-bs-theme="dark"] .nav-brand {
            color: #fff;
            border-right-color: #3f3f46;
        }
        [data-bs-theme="dark"] .nav-tabs-custom .nav-link {
            color: #a1a1aa;
        }
        [data-bs-theme="dark"] .nav-tabs-custom .nav-link:hover {
            background: #3f3f46;
            color: #fff;
        }
        [data-bs-theme="dark"] .nav-tabs-custom .nav-link.active {
            background: #27272a;
            color: #fff;
            border-color: #3f3f46 #3f3f46 #27272a;
        }
        [data-bs-theme="dark"] .task-table th {
            background-color: #3f3f46;
            color: #fff;
            border-bottom-color: #52525b;
        }
        [data-bs-theme="dark"] .task-table td {
            border-color: #3f3f46;
            color: #e4e4e7;
        }
        [data-bs-theme="dark"] .task-table tbody tr:hover {
            background-color: #3f3f46;
        }
        [data-bs-theme="dark"] .modal-content {
            background-color: #27272a;
        }
        [data-bs-theme="dark"] .modal-header-custom {
            background: #18181b;
        }
        [data-bs-theme="dark"] .page-link {
            background-color: #27272a;
            border-color: #3f3f46;
            color: #a1a1aa;
        }
        [data-bs-theme="dark"] .page-link:hover {
            background-color: #3f3f46;
            color: #fff;
        }
        [data-bs-theme="dark"] .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: #fff;
        }
        [data-bs-theme="dark"] .page-item.disabled .page-link {
            background-color: #27272a;
            border-color: #3f3f46;
            color: #52525b;
        }
        [data-bs-theme="dark"] .card-hover:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.3) !important;
        }
        [data-bs-theme="dark"] .card[style*="background:#f5f3ff;"] { background: #221e30 !important; border: 1px solid #342c4a !important; box-shadow: none !important; }
        [data-bs-theme="dark"] .card[style*="background:#eef2ff;"] { background: #1e1e2d !important; border: 1px solid #2c2c44 !important; box-shadow: none !important; }
        [data-bs-theme="dark"] .card[style*="background:#fff7ed;"] { background: #291e18 !important; border: 1px solid #422b1e !important; box-shadow: none !important; }
        [data-bs-theme="dark"] .card[style*="background:#f0fdf4;"] { background: #18241b !important; border: 1px solid #243d2a !important; box-shadow: none !important; }
        [data-bs-theme="dark"] .card[style*="background:#f0f9ff;"] { background: #16222b !important; border: 1px solid #1e3648 !important; box-shadow: none !important; }
    </style>
</head>

<body>

    <!-- ── Navigation Tabs ── -->
    <nav class="nav-tabs-custom">
        <!-- App Brand / Home link -->
        <a href="{{ route('home') }}" class="nav-brand {{ request()->routeIs('home') ? 'active-brand' : '' }}">
            <img src="{{ asset('images/AccompList.png') }}?v={{ time() }}" alt="Logo">
            AccompList
        </a>

        <ul class="nav nav-tabs border-0">
            <li class="nav-item">
                <a class="nav-link {{ request()->is('tasks') && request()->query('status') === 'To Do' ? 'active' : '' }}"
                    href="{{ route('tasks.index', ['status' => 'To Do']) }}">
                    <i class="bi bi-list-check me-1"></i>To Do
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->query('status') === 'In Progress' ? 'active' : '' }}"
                    href="{{ route('tasks.index', ['status' => 'In Progress']) }}">
                    <i class="bi bi-arrow-repeat me-1"></i>In Progress
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->query('status') === 'Completed' ? 'active' : '' }}"
                    href="{{ route('tasks.index', ['status' => 'Completed']) }}">
                    <i class="bi bi-check2-circle me-1"></i>Completed
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->query('status') === 'Submitted' ? 'active' : '' }}"
                    href="{{ route('tasks.index', ['status' => 'Submitted']) }}">
                    <i class="bi bi-send-check me-1"></i>Submitted
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('tasks.manage') ? 'active' : '' }}"
                    href="{{ route('tasks.manage') }}">
                    <i class="bi bi-gear me-1"></i>Manage Lists
                </a>
            </li>
        </ul>

        <!-- Dark Mode Toggle -->
        <div class="ms-auto pe-2">
            <button id="themeToggle" class="btn btn-sm text-secondary" style="border: none; background: transparent; font-size: 1.2rem;" title="Toggle Dark Mode">
                <i class="bi bi-moon-stars"></i>
            </button>
        </div>
    </nav>

    <!-- ── Page Content ── -->
    <div class="container-fluid px-4">

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-flash alert-dismissible fade show mt-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-flash alert-dismissible fade show mt-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <!-- ── Add New Task Modal ── -->
    <div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title fw-bold" id="addTaskModalLabel">
                        <i class="bi bi-plus-circle me-2"></i>Add New Task
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <form action="{{ route('tasks.store') }}" method="POST" id="addTaskForm">
                        @csrf

                        {{-- Task Name --}}
                        <div class="mb-3">
                            <label for="modal_task_name" class="form-label fw-semibold">
                                Task Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('task_name') is-invalid @enderror"
                                id="modal_task_name" name="task_name" value="{{ old('task_name') }}"
                                placeholder="e.g. Logo Design" autofocus>
                            @error('task_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Priority --}}
                        <div class="mb-3">
                            <label for="modal_priority" class="form-label fw-semibold">
                                Priority <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('priority') is-invalid @enderror" id="modal_priority"
                                name="priority">
                                <option value="">-- Select Priority --</option>
                                @foreach(['High', 'Medium', 'Low'] as $p)
                                    <option value="{{ $p }}" {{ old('priority') === $p ? 'selected' : '' }}>
                                        {{ $p }}
                                    </option>
                                @endforeach
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Deadline --}}
                        <div class="mb-3">
                            <label for="modal_deadline" class="form-label fw-semibold">
                                Deadline <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control @error('deadline') is-invalid @enderror"
                                id="modal_deadline" name="deadline" value="{{ old('deadline') }}">
                            @error('deadline')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Status --}}
                        <div class="mb-3">
                            <label for="modal_status" class="form-label fw-semibold">
                                Status <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('status') is-invalid @enderror" id="modal_status"
                                name="status">
                                <option value="">-- Select Status --</option>
                                @foreach(['To Do', 'In Progress', 'Completed', 'Submitted'] as $s)
                                    <option value="{{ $s }}" {{ old('status', 'To Do') === $s ? 'selected' : '' }}>
                                        {{ $s }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div class="mb-4">
                            <label for="modal_description" class="form-label fw-semibold">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                id="modal_description" name="description" rows="3"
                                placeholder="Optional notes about this task...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Buttons --}}
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-add-task">
                                <i class="bi bi-floppy me-1"></i>Save Task
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- ── Edit Task Modal ── -->
    <div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header modal-header-custom" style="background: #198754;">
                    <h5 class="modal-title fw-bold" id="editTaskModalLabel">
                        <i class="bi bi-pencil-square me-2"></i>Update Task
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <form action="" method="POST" id="editTaskForm">
                        @csrf
                        @method('PUT')

                        {{-- Task Name --}}
                        <div class="mb-3">
                            <label for="edit_task_name" class="form-label fw-semibold">
                                Task Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('edit_task_name') is-invalid @enderror"
                                id="edit_task_name" name="task_name" value="{{ old('task_name') }}" autofocus>
                            @error('edit_task_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Priority --}}
                        <div class="mb-3">
                            <label for="edit_priority" class="form-label fw-semibold">
                                Priority <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('edit_priority') is-invalid @enderror" id="edit_priority"
                                name="priority">
                                @foreach(['High', 'Medium', 'Low'] as $p)
                                    <option value="{{ $p }}" {{ old('priority') === $p ? 'selected' : '' }}>
                                        {{ $p }}
                                    </option>
                                @endforeach
                            </select>
                            @error('edit_priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Deadline --}}
                        <div class="mb-3">
                            <label for="edit_deadline" class="form-label fw-semibold">
                                Deadline <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control @error('edit_deadline') is-invalid @enderror"
                                id="edit_deadline" name="deadline" value="{{ old('deadline') }}">
                            @error('edit_deadline')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Status --}}
                        <div class="mb-3">
                            <label for="edit_status" class="form-label fw-semibold">
                                Status <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('edit_status') is-invalid @enderror" id="edit_status"
                                name="status">
                                @foreach(['To Do', 'In Progress', 'Completed', 'Submitted'] as $s)
                                    <option value="{{ $s }}" {{ old('status') === $s ? 'selected' : '' }}>
                                        {{ $s }}
                                    </option>
                                @endforeach
                            </select>
                            @error('edit_status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div class="mb-4">
                            <label for="edit_description" class="form-label fw-semibold">Description</label>
                            <textarea class="form-control @error('edit_description') is-invalid @enderror"
                                id="edit_description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('edit_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Buttons --}}
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-update">
                                <i class="bi bi-floppy me-1"></i>Update Task
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Auto-reopen modal if validation failed --}}
    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Check if it's the edit form that failed (by checking if old input has a specific flag or just checking if we were editing)
                // For simplicity, if old('_method') is 'PUT', it was the edit form.
                @if(old('_method') == 'PUT')
                    var editForm = document.getElementById('editTaskForm');
                    // Re-construct the action URL
                    var taskId = '{{ old("task_id") }}'; // We need to pass this as a hidden field
                    if (taskId) {
                        editForm.action = '/tasks/' + taskId;
                    }
                    var editModal = new bootstrap.Modal(document.getElementById('editTaskModal'));
                    editModal.show();
                @else
                                    var addModal = new bootstrap.Modal(document.getElementById('addTaskModal'));
                    addModal.show();
                @endif
                });
        </script>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Handle Edit Task buttons
            const editButtons = document.querySelectorAll('.btn-edit-task');
            const editForm = document.getElementById('editTaskForm');

            editButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const id = this.dataset.id;
                    const name = this.dataset.name;
                    const priority = this.dataset.priority;
                    const deadline = this.dataset.deadline;
                    const status = this.dataset.status;
                    const description = this.dataset.description;

                    // Update form action
                    editForm.action = `/tasks/${id}`;

                    // Add hidden input for task_id to handle validation errors
                    let idInput = editForm.querySelector('input[name="task_id"]');
                    if (!idInput) {
                        idInput = document.createElement('input');
                        idInput.type = 'hidden';
                        idInput.name = 'task_id';
                        editForm.appendChild(idInput);
                    }
                    idInput.value = id;

                    // Populate fields
                    document.getElementById('edit_task_name').value = name;
                    document.getElementById('edit_priority').value = priority;
                    document.getElementById('edit_deadline').value = deadline;
                    document.getElementById('edit_status').value = status;
                    document.getElementById('edit_description').value = description || '';
                });
            });
        });
    </script>

    {{-- Dark Mode Logic --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('themeToggle');
            const icon = toggleBtn.querySelector('i');

            // Initial icon setup
            if (document.documentElement.getAttribute('data-bs-theme') === 'dark') {
                icon.classList.replace('bi-moon-stars', 'bi-sun');
            }

            toggleBtn.addEventListener('click', function() {
                let currentTheme = document.documentElement.getAttribute('data-bs-theme');
                if (currentTheme === 'dark') {
                    document.documentElement.setAttribute('data-bs-theme', 'light');
                    localStorage.setItem('theme', 'light');
                    icon.classList.replace('bi-sun', 'bi-moon-stars');
                } else {
                    document.documentElement.setAttribute('data-bs-theme', 'dark');
                    localStorage.setItem('theme', 'dark');
                    icon.classList.replace('bi-moon-stars', 'bi-sun');
                }
            });
        });
    </script>

</body>

</html>