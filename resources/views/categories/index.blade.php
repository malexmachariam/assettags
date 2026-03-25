@extends('categories.layout')

@section('category-content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Categories</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCategoryModal">Create Category</button>
</div>
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($categories as $category)
        <tr>
            <td>{{ $category->name }}</td>
            <td>{{ ucfirst($category->category_type) }}</td>
            <td>
                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editCategoryModal{{ $category->id }}">Edit</button>
                <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                </form>
            </td>
        </tr>
        <!-- Edit Modal -->
        <div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1" aria-labelledby="editCategoryModalLabel{{ $category->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editCategoryModalLabel{{ $category->id }}">Edit Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('categories.update', $category) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="name{{ $category->id }}" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name{{ $category->id }}" name="name" value="{{ $category->name }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="category_type{{ $category->id }}" class="form-label">Category Type</label>
                                <select class="form-select" id="category_type{{ $category->id }}" name="category_type" required>
                                    <option value="asset" {{ $category->category_type == 'asset' ? 'selected' : '' }}>Asset</option>
                                    <option value="accessory" {{ $category->category_type == 'accessory' ? 'selected' : '' }}>Accessory</option>
                                    <option value="consumable" {{ $category->category_type == 'consumable' ? 'selected' : '' }}>Consumable</option>
                                    <option value="component" {{ $category->category_type == 'component' ? 'selected' : '' }}>Component</option>
                                    <option value="license" {{ $category->category_type == 'license' ? 'selected' : '' }}>License</option>
                                </select>
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
<div class="modal fade" id="createCategoryModal" tabindex="-1" aria-labelledby="createCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createCategoryModalLabel">Create Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('categories.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="category_type" class="form-label">Category Type</label>
                        <select class="form-select" id="category_type" name="category_type" required>
                            <option value="asset">Asset</option>
                            <option value="accessory">Accessory</option>
                            <option value="consumable">Consumable</option>
                            <option value="component">Component</option>
                            <option value="license">License</option>
                        </select>
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
