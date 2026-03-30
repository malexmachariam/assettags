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
                        <a href="{{ route('assets.create') }}" class="btn btn-outline-primary">Record Another Asset</a>
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">Back Home</a>
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
