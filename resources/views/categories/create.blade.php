@extends('categories.layout')

@section('category-content')
<div class="card">
    <div class="card-header">Create Category</div>
    <div class="card-body">
        <form method="POST" action="{{ route('categories.store') }}">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
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
                    <option value="asset">Asset</option>
                    <option value="accessory">Accessory</option>
                    <option value="consumable">Consumable</option>
                    <option value="component">Component</option>
                    <option value="license">License</option>
                </select>
                @error('category_type')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <button type="submit" class="btn btn-success">Create</button>
        </form>
    </div>
</div>
@endsection
