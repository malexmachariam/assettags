@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">User Management</div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    <form method="POST" action="{{ route('users.store') }}" class="mb-4">
                        @csrf
                        <div class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="col-md-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="col-md-3">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="user">Normal User</option>
                                    <option value="allocator">Allocator</option>
                                    <option value="super_admin">Super Admin</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="col-md-12 mt-2">
                                <button type="submit" class="btn btn-success">Add User</button>
                            </div>
                        </div>
                    </form>

                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('users.updateRole', $user) }}" class="d-flex align-items-center gap-1">
                                            @csrf
                                            @method('PATCH')
                                            <select name="role" class="form-select form-select-sm" style="width:auto; min-width:120px;" onchange="this.form.submit()">
                                                <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>Normal User</option>
                                                <option value="allocator" {{ $user->role === 'allocator' ? 'selected' : '' }}>Allocator</option>
                                                <option value="super_admin" {{ $user->role === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td>
                                        @if($user->active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!$user->active)
                                            <form method="POST" action="{{ route('users.activate', $user) }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-success">Activate</button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('users.deactivate', $user) }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-warning">Deactivate</button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('users.destroy', $user) }}" class="d-inline ms-1">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete user?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
