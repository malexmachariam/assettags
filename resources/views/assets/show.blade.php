@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">Asset Details</div>
                <div class="card-body">
                    <table class="table table-bordered mb-0 align-middle">
                        <tbody>
                                                        <tr>
                                                            <th>Allocated To</th>
                                                            <td>
                                                                @if($asset->allocated_to && $asset->allocated_name)
                                                                    <span class="badge bg-primary text-uppercase">{{ $asset->allocated_to }}</span>
                                                                    <span class="ms-2">{{ $asset->allocated_name }}</span>
                                                                @else
                                                                    <span class="text-muted">Not allocated</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                            <tr>
                                <th style="width: 30%;">Asset Name</th>
                                <td>{{ $asset->name }}</td>
                            </tr>
                            <tr>
                                <th>Asset Tag</th>
                                <td>{{ $asset->asset_tag }}</td>
                            </tr>
                            <tr>
                                <th>Serial Number</th>
                                <td>{{ $asset->serial_number ?: 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Category</th>
                                <td>{{ optional(optional($asset->assetModel)->category)->name ?: 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Asset Model</th>
                                <td>{{ optional($asset->assetModel)->name ?: 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Batch</th>
                                <td>{{ optional($asset->batch)->name ?: 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Description</th>
                                <td>{{ $asset->description ?: 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Created</th>
                                <td>{{ optional($asset->created_at)->format('Y-m-d H:i') ?: 'N/A' }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="mt-3 d-flex gap-2 flex-wrap">
                        <a href="{{ route('assets.tag.pdf', $asset) }}" target="_blank" rel="noopener" class="btn btn-outline-danger">Show Tag PDF</a>
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#allocateModal">Allocate</button>
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">Back Home</a>
                    </div>

                    <!-- Allocate Modal -->
                    <div class="modal fade" id="allocateModal" tabindex="-1" aria-labelledby="allocateModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="allocateModalLabel">Allocate Asset</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form method="POST" action="{{ route('assets.allocate', $asset) }}">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="allocated_to" class="form-label">Allocate To</label>
                                            <select class="form-select" id="allocated_to" name="allocated_to" required>
                                                <option value="">Select Type</option>
                                                <option value="person" {{ $asset->allocated_to === 'person' ? 'selected' : '' }}>Person</option>
                                                <option value="station" {{ $asset->allocated_to === 'station' ? 'selected' : '' }}>Station</option>
                                                <option value="division" {{ $asset->allocated_to === 'division' ? 'selected' : '' }}>Division</option>
                                                <option value="department" {{ $asset->allocated_to === 'department' ? 'selected' : '' }}>Department</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="allocated_name" class="form-label">Allocated Name</label>
                                            <input type="text" class="form-control" id="allocated_name" name="allocated_name" placeholder="Enter name" value="{{ $asset->allocated_name }}" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Allocate</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">Asset Tag Preview</div>
                <div class="card-body d-flex justify-content-center">
                    {!! $asset->asset_tag_html !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
