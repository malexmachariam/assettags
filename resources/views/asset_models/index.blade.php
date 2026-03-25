@extends('categories.layout')

@section('category-content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Asset Models</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAssetModelModal">Create Asset Model</button>
</div>
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Name</th>
            <th>Category</th>
            <th>Model No</th>
            <th>Require Serial Number</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($assetModels as $model)
        <tr>
            <td>{{ $model->name }}</td>
            <td>{{ $model->category->name ?? '' }}</td>
            <td>{{ $model->model_no }}</td>
            <td>{{ $model->require_serial_number ? 'Yes' : 'No' }}</td>
            <td>
                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editAssetModelModal{{ $model->id }}">Edit</button>
                <form action="{{ route('asset-models.destroy', $model) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                </form>
            </td>
        </tr>
        <!-- Edit Modal -->
        <div class="modal fade" id="editAssetModelModal{{ $model->id }}" tabindex="-1" aria-labelledby="editAssetModelModalLabel{{ $model->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editAssetModelModalLabel{{ $model->id }}">Edit Asset Model</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('asset-models.update', $model) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="name{{ $model->id }}" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name{{ $model->id }}" name="name" value="{{ $model->name }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="category_id{{ $model->id }}" class="form-label">Category</label>
                                <select class="form-select" id="category_id{{ $model->id }}" name="category_id" required>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ $model->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="model_no{{ $model->id }}" class="form-label">Model No</label>
                                <input type="text" class="form-control" id="model_no{{ $model->id }}" name="model_no" value="{{ $model->model_no }}">
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="require_serial_number{{ $model->id }}" name="require_serial_number" value="1" {{ $model->require_serial_number ? 'checked' : '' }}>
                                <label class="form-check-label" for="require_serial_number{{ $model->id }}">Require Serial Number</label>
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
        @endforeach
    </tbody>
</table>
<!-- Create Modal -->
<div class="modal fade" id="createAssetModelModal" tabindex="-1" aria-labelledby="createAssetModelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createAssetModelModalLabel">Create Asset Model</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('asset-models.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="model_no" class="form-label">Model No</label>
                        <input type="text" class="form-control" id="model_no" name="model_no">
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="require_serial_number" name="require_serial_number" value="1">
                        <label class="form-check-label" for="require_serial_number">Require Serial Number</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
