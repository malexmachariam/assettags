@extends('layouts.app')

@section('content')
<div class="container mt-5">
    @if($activeBatch)
        <div class="alert alert-info">Current active batch: <strong>{{ $activeBatch->name }}</strong></div>
    @else
        <div class="alert alert-warning">No active batch selected. Create and activate a batch before recording assets.</div>
    @endif

    <div class="row justify-content-center">
        {{-- <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">Record New Asset</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('assets.store') }}">
                        @csrf

                        <div class="form-group mb-3">
                            <label for="asset_model_id">Asset Model</label>
                            <select class="form-select" id="asset_model_id" name="asset_model_id" required>
                                <option value="">Select Asset Model</option>
                                @foreach($assetModels as $model)
                                    <option value="{{ $model->id }}" 
                                            data-require-serial="{{ $model->require_serial_number ? '1' : '0' }}">
                                        {{ $model->name }} ({{ $model->category->name ?? 'No Category' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-3" id="serial_number_group" style="display: none;">
                            <label for="serial_number">Serial Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="serial_number" name="serial_number">
                        </div>

                        <div class="form-group mb-3">
                            <label for="name">Asset Name </label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Save Asset</button>
                    </form>
                </div>
            </div>
        </div> --}}

        {{-- <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-success text-white">Bulk Generate Non-Serial Assets</div>
                <div class="card-body">
                    @if($bulkAssetModels->isEmpty())
                        <div class="alert alert-warning mb-0">No asset models are currently configured without serial numbers.</div>
                    @else
                        <form method="POST" action="{{ route('assets.bulk-store') }}">
                            @csrf

                            <div class="form-group mb-3">
                                <label for="bulk_asset_model_id">Asset Model</label>
                                <select class="form-select" id="bulk_asset_model_id" name="bulk_asset_model_id" required>
                                    <option value="">Select Non-Serial Asset Model</option>
                                    @foreach($bulkAssetModels as $model)
                                        <option value="{{ $model->id }}" {{ old('bulk_asset_model_id') == $model->id ? 'selected' : '' }}>
                                            {{ $model->name }} ({{ $model->category->name ?? 'No Category' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="bulk_quantity">Quantity</label>
                                <input type="number" min="1" max="200" class="form-control" id="bulk_quantity" name="bulk_quantity" value="{{ old('bulk_quantity', 1) }}" required>
                                <small class="text-muted">This will insert that many asset records using the selected asset model.</small>
                            </div>

                            <div class="form-group mb-3">
                                <label for="bulk_description">Description</label>
                                <textarea class="form-control" id="bulk_description" name="bulk_description" rows="3">{{ old('bulk_description') }}</textarea>
                                <small class="text-muted">Each generated asset will use the asset model name as its asset name.</small>
                            </div>

                            <button type="submit" class="btn btn-success">Generate Assets</button>
                        </form>
                    @endif
                </div>
            </div>
        </div> --}}

        <div class="col-lg-12 mb-4">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">Import Serial-Number Assets From CSV</div>
                <div class="card-body">
                    @if($serialAssetModels->isEmpty())
                        <div class="alert alert-warning mb-0">No asset models are currently configured to require serial numbers.</div>
                    @else
                        <form method="POST" action="{{ route('assets.import-serial-csv') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="row g-3">
                                <div class="col-md-5">
                                    <label for="serial_asset_model_id" class="form-label">Asset Model</label>
                                    <select class="form-select" id="serial_asset_model_id" name="serial_asset_model_id" required>
                                        <option value="">Select Serial-Required Asset Model</option>
                                        @foreach($serialAssetModels as $model)
                                            <option value="{{ $model->id }}" {{ old('serial_asset_model_id') == $model->id ? 'selected' : '' }}>
                                                {{ $model->name }} ({{ $model->category->name ?? 'No Category' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-5">
                                    <label for="serial_csv" class="form-label">CSV File</label>
                                    <input type="file" class="form-control" id="serial_csv" name="serial_csv" accept=".csv,.txt" required>
                                    <small class="text-muted">Use first column for serial numbers. Header is optional (e.g., serial_number).</small>
                                </div>

                                <div class="col-md-2 d-grid align-items-end">
                                    <button type="submit" class="btn btn-warning">Import CSV</button>
                                </div>
                            </div>

                            <div class="form-group mt-3">
                                <label for="serial_description">Description</label>
                                <textarea class="form-control" id="serial_description" name="serial_description" rows="2">{{ old('serial_description') }}</textarea>
                                <small class="text-muted">This description will be applied to all imported assets.</small>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modelSelect = document.getElementById('asset_model_id');
    const serialGroup = document.getElementById('serial_number_group');
    const serialInput = document.getElementById('serial_number');

    modelSelect.addEventListener('change', function() {
        const selectedOption = modelSelect.options[modelSelect.selectedIndex];
        const requireSerial = selectedOption && selectedOption.dataset.requireSerial === '1';

        if (requireSerial) {
            serialGroup.style.display = 'block';
            serialInput.required = true;
        } else {
            serialGroup.style.display = 'none';
            serialInput.required = false;
            serialInput.value = ''; // clear it
        }
    });

    // Trigger once on load in case of browser back/refresh
    modelSelect.dispatchEvent(new Event('change'));
});
</script>
@endsection