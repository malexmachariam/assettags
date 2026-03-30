<div class="card shadow-sm h-100 border-0">
    <div class="card-body text-center p-4">
        {{-- <h5 class="mb-3">{{ $asset->name }}</h5> --}}
        {!! $asset->asset_tag_html !!}
        <div class="mt-4">
            {{-- <button onclick="printFromModal()" class="btn btn-primary px-5 py-2">
                <i class="bi bi-printer me-2"></i> Print Tag
            </button> --}}
        </div>
    </div>
</div>