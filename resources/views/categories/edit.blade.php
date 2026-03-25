@extends('categories.layout')

@section('category-content')
<div class="card">
    <div class="card-header">Edit Category</div>
    <div class="card-body">
        <form method="POST" action="{{ route('categories.update', $category) }}">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $category->name) }}" required>
                @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="mb-3">
                <label for="category_type" class="form-label">Category Type</label>
                <select class="form-select @error('category_type') is-invalid @enderror" id="category_type" name="category_type" required>
                    <option value="">Select Type</option>
                    <option value="asset" {{ old('category_type', $category->category_type) == 'asset' ? 'selected' : '' }}>Asset</option>
                    <option value="accessory" {{ old('category_type', $category->category_type) == 'accessory' ? 'selected' : '' }}>Accessory</option>
                    <option value="consumable" {{ old('category_type', $category->category_type) == 'consumable' ? 'selected' : '' }}>Consumable</option>
                    <option value="component" {{ old('category_type', $category->category_type) == 'component' ? 'selected' : '' }}>Component</option>
                    <option value="license" {{ old('category_type', $category->category_type) == 'license' ? 'selected' : '' }}>License</option>
                </select>
                @error('category_type')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>
@endsection
