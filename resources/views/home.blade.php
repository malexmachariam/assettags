@extends('layouts.app')

@section('content')
<div class="container">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">All Assets</h3>
        <a href="{{ route('assets.create') }}" class="btn btn-primary shadow">Add New Asset</a>
    </div>

    <!-- Assets Table -->
    <div class="card shadow rounded-4 mb-5" style="border: none;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="datatablesSimple" class="table table-hover align-middle mb-0" style="border-radius: 12px; overflow: hidden;">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th>Name</th>
                            <th>Asset Model</th>
                            <th>Asset Tag</th>
                            <th>Description</th>
                            <th style="width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($assets as $asset)
                            <tr>
                                <td class="text-muted">{{ $asset->id }}</td>
                                <td><strong>{{ $asset->name }}</strong></td>
                                <td style="font-size: 0.95em; word-break: break-all;">{{ $asset->assetModel->name ?? '—' }}</td>
                                <td style="font-size: 0.95em; word-break: break-all;">{{ $asset->asset_tag }}</td>
                                <td style="max-width: 250px; white-space: pre-line;">{{ $asset->description ?? '—' }}</td>
                                <td>
                                    <a href="{{ route('assets.show', $asset) }}" class="btn btn-sm btn-outline-info">View</a>
                                    <button type="button" class="btn btn-sm btn-info ms-1" onclick="showAssetTagModal({{ $asset->id }})">Show Tag</button>
                                    <a href="{{ route('assets.tag.pdf', $asset) }}" class="btn btn-sm btn-outline-danger ms-1" target="_blank" rel="noopener">Show PDF</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No assets found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mb-5">
        {{ $assets->links() }}
    </div>


    <!-- Asset Tag Modal -->
    <div class="modal fade" id="assetTagModal" tabindex="-1" aria-labelledby="assetTagModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assetTagModalLabel">Asset Tag Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center" id="assetTagModalBody">
                    <div class="text-muted">Loading...</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a id="modalPdfBtn" href="#" class="btn btn-outline-danger" target="_blank" rel="noopener">
                        <i class="bi bi-file-earmark-pdf"></i> Download PDF
                    </a>
                    <button type="button" onclick="printFromModal()" class="btn btn-primary">
                        <i class="bi bi-printer"></i> Print
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
function showAssetTagModal(assetId) {
    document.getElementById('assetTagModalBody').innerHTML = '<div class="text-muted">Loading...</div>';
    document.getElementById('modalPdfBtn').href = '/assets/' + assetId + '/tag/pdf';

    var modal = new bootstrap.Modal(document.getElementById('assetTagModal'));
    modal.show();

    fetch('/assets/' + assetId + '/tag')
        .then(response => response.text())
        .then(html => {
            document.getElementById('assetTagModalBody').innerHTML = html;
        })
        .catch(() => {
            document.getElementById('assetTagModalBody').innerHTML = '<div class="text-danger">Failed to load tag.</div>';
        });
}

function printFromModal() {
    const tagHTML = document.getElementById('assetTagModalBody').innerHTML;
    const printWin = window.open('', '_blank');
    printWin.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Print Asset Tag</title>
            <style>
                body { font-family: Arial, sans-serif; text-align: center; padding: 40px; background: #f8f9fa; }
                .tag-container { display: inline-block; background: white; }
                @media print { body { padding: 0; background: white; } }
            </style>
        </head>
        <body>
            <div class="tag-container">${tagHTML}</div>
        </body>
        </html>
    `);
    printWin.document.close();
    printWin.focus();
    setTimeout(() => { printWin.print(); }, 500);
}
</script>

@endsection