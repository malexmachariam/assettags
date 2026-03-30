@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">Create Batch</div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form method="POST" action="{{ route('batches.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Batch Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" value="1" id="active" name="active" {{ old('active') ? 'checked' : '' }}>
                            <label class="form-check-label" for="active">Set as active batch</label>
                        </div>

                        <button type="submit" class="btn btn-primary">Save Batch</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <span>All Batches</span>
                    <span class="badge bg-success">Active: {{ $activeBatch?->name ?? 'None' }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Assets</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($batches as $batch)
                                    <tr>
                                        <td>{{ $batch->name }}</td>
                                        <td>{{ $batch->assets_count }}</td>
                                        <td>
                                            @if($batch->active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(! $batch->active)
                                                <form method="POST" action="{{ route('batches.activate', $batch) }}" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-outline-primary">Set Active</button>
                                                </form>
                                            @endif
                                            <a href="{{ route('batches.tags.pdf', $batch) }}" class="btn btn-sm btn-outline-danger ms-1" target="_blank" rel="noopener">Tags PDF</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">No batches created yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

            {{-- Assign unassigned assets --}}
            @if($unassignedCount > 0)
            <div class="card shadow-sm mt-4 border-warning">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <span><strong>Unassigned Assets</strong></span>
                    <span class="badge bg-dark">{{ $unassignedCount }} asset(s) have no batch</span>
                </div>
                <div class="card-body">
                    <p class="mb-3 text-muted small">Select a batch below to assign all unassigned assets to it.</p>
                    @if($batches->isEmpty())
                        <p class="text-danger small">No batches available. Create a batch first.</p>
                    @else
                        <form method="POST" id="assignUnassignedForm">
                            @csrf
                            <div class="input-group">
                                <select class="form-select" id="assignBatchSelect" required>
                                    <option value="" disabled selected>-- Select a batch --</option>
                                    @foreach($batches as $batch)
                                        <option value="{{ route('batches.assign-unassigned', $batch) }}">
                                            {{ $batch->name }}{{ $batch->active ? ' (Active)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-warning">Assign</button>
                            </div>
                        </form>
                        <script>
                            document.getElementById('assignUnassignedForm').addEventListener('submit', function (e) {
                                e.preventDefault();
                                var url = document.getElementById('assignBatchSelect').value;
                                if (!url) return;
                                this.action = url;
                                this.submit();
                            });
                        </script>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection