@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-success text-white">Asset Details & QR Code</div>
                <div class="card-body text-center">
                    <h5>{{ $asset->name }}</h5>
                    <p><strong>Asset Tag:</strong> {{ $asset->uuid }}</p>
                    <p>{{ $asset->description }}</p>
                    <div class="my-4">
                        <img src="{{ $qrWithLogo }}" alt="QR Code with Logo" style="width: 300px; height: 300px;">
                    </div>
                    <a href="{{ route('assets.create') }}" class="btn btn-outline-primary">Record Another Asset</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
