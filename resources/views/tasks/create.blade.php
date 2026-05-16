@extends('tasks.layout')

@section('content')
<div class="content-card" style="max-width:600px;">

    <h5 class="fw-bold mb-4">
        <i class="bi bi-plus-circle me-2 text-primary"></i>Add New Task
    </h5>

    <form action="{{ route('tasks.store') }}" method="POST">
        @csrf

        {{-- Task Name --}}
        <div class="mb-3">
            <label for="task_name" class="form-label fw-semibold">
                Task Name <span class="text-danger">*</span>
            </label>
            <input type="text"
                   class="form-control @error('task_name') is-invalid @enderror"
                   id="task_name" name="task_name"
                   value="{{ old('task_name') }}"
                   placeholder="e.g. Logo Design"
                   autofocus>
            @error('task_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Priority --}}
        <div class="mb-3">
            <label for="priority" class="form-label fw-semibold">
                Priority <span class="text-danger">*</span>
            </label>
            <select class="form-select @error('priority') is-invalid @enderror"
                    id="priority" name="priority">
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
            <label for="deadline" class="form-label fw-semibold">
                Deadline <span class="text-danger">*</span>
            </label>
            <input type="date"
                   class="form-control @error('deadline') is-invalid @enderror"
                   id="deadline" name="deadline"
                   value="{{ old('deadline') }}">
            @error('deadline')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Status --}}
        <div class="mb-3">
            <label for="status" class="form-label fw-semibold">
                Status <span class="text-danger">*</span>
            </label>
            <select class="form-select @error('status') is-invalid @enderror"
                    id="status" name="status">
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
            <label for="description" class="form-label fw-semibold">Description</label>
            <textarea class="form-control @error('description') is-invalid @enderror"
                      id="description" name="description"
                      rows="3"
                      placeholder="Optional notes about this task...">{{ old('description') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Buttons --}}
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-add-task">
                <i class="bi bi-floppy me-1"></i>Save Task
            </button>
            <a href="{{ route('tasks.index', ['status' => 'To Do']) }}"
               class="btn btn-outline-secondary">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
