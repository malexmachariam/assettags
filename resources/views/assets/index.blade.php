@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Assets</h2>
                <a href="{{ route('assets.create') }}" class="btn btn-primary">Add Asset</a>
            </div>
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Model</th>
                        <th>Serial Number</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($assets as $asset)
                    <tr>
                        <td>{{ $asset->name }}</td>
                        <td>{{ $asset->assetModel->name ?? '' }}</td>
                        <td>{{ $asset->serial_number }}</td>
                        <td>{{ $asset->description }}</td>
                        <td>
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editAssetModal{{ $asset->id }}">Edit</button>
                            <form action="{{ route('assets.destroy', $asset) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                            <a href="{{ route('assets.show', $asset) }}" class="btn btn-sm btn-info mt-1">View QR</a>
                        </td>
                    </tr>
                    <!-- Edit Modal -->
                    <div class="modal fade" id="editAssetModal{{ $asset->id }}" tabindex="-1" aria-labelledby="editAssetModalLabel{{ $asset->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editAssetModalLabel{{ $asset->id }}">Edit Asset</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form method="POST" action="{{ route('assets.update', $asset) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <div class="form-group mb-3">
                                            <label for="name{{ $asset->id }}">Asset Name</label>
                                            <input type="text" class="form-control" id="name{{ $asset->id }}" name="name" value="{{ $asset->name }}" required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="asset_model_id{{ $asset->id }}">Asset Model</label>
                                            <select class="form-select" id="asset_model_id{{ $asset->id }}" name="asset_model_id" required>
                                                @foreach($assetModels as $model)
                                                    <option value="{{ $model->id }}" data-require-serial="{{ $model->require_serial_number ? '1' : '0' }}" {{ $asset->asset_model_id == $model->id ? 'selected' : '' }}>
                                                        {{ $model->name }} ({{ $model->category->name ?? '' }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group mb-3 serial_number_group" id="serial_number_group{{ $asset->id }}" style="display:none;">
                                            <label for="serial_number{{ $asset->id }}">Serial Number</label>
                                            <input type="text" class="form-control" id="serial_number{{ $asset->id }}" name="serial_number" value="{{ $asset->serial_number }}">
                                            <small class="form-text text-muted">Leave blank to auto-generate a unique code.</small>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="description{{ $asset->id }}">Description</label>
                                            <textarea class="form-control" id="description{{ $asset->id }}" name="description" rows="2">{{ $asset->description }}</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const modelSelect = document.getElementById('asset_model_id{{ $asset->id }}');
                        const serialGroup = document.getElementById('serial_number_group{{ $asset->id }}');
                        const serialInput = document.getElementById('serial_number{{ $asset->id }}');
                        function toggleSerial() {
                            const selected = modelSelect.options[modelSelect.selectedIndex];
                            if (selected && selected.dataset.requireSerial === '1') {
                                serialGroup.style.display = '';
                                serialInput.required = true;
                            } else {
                                serialGroup.style.display = 'none';
                                serialInput.required = false;
                            }
                        }
                        modelSelect.addEventListener('change', toggleSerial);
                        toggleSerial();
                    });
                    </script>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
