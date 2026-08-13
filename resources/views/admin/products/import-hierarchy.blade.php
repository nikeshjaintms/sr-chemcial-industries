@extends('admin.layouts.admin')

@section('title', 'Bulk Product Hierarchy Import — SRCIL Admin ERP')
@section('page_title', 'Bulk Product Hierarchy Import')

@section('content')
<div class="card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="h5 font-bold text-dark mb-1">
                <i class="fa-solid fa-file-import text-primary me-2"></i> Bulk Product Hierarchy Importer
            </h2>
            <p class="text-13 text-muted mb-0">Paste your ASCII product tree structure below. The parser automatically creates nested category branches and products with duplicate protection.</p>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-sitemap me-1"></i> View Hierarchy Tree
        </a>
    </div>

    <!-- Workflow Progress Bar -->
    <div class="row text-center mb-4 g-2">
        <div class="col-md-4">
            <div class="p-3 bg-light rounded border text-13 font-semibold text-primary">
                <i class="fa-solid fa-paste me-1"></i> 1. Paste Hierarchy Tree
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3 bg-light rounded border text-13 font-semibold text-warning">
                <i class="fa-solid fa-eye me-1"></i> 2. Preview Hierarchy
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3 bg-light rounded border text-13 font-semibold text-success">
                <i class="fa-solid fa-cloud-arrow-up me-1"></i> 3. Import &amp; Save to DB
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger mb-4">{{ session('error') }}</div>
    @endif

    <!-- CORE PHP AUTOMATIC PRODUCT & ASSET MIGRATION BANNER -->
    <div class="card bg-primary-subtle border-primary mb-4 p-3 rounded-3">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h3 class="h6 font-bold text-primary mb-1">
                    <i class="fa-solid fa-database me-2"></i> Auto-Migrate from Core PHP Source (<code>C:\xampp\htdocs\SR</code>)
                </h3>
                <p class="text-12 text-dark mb-0">
                    Directly fetch all 87+ existing products, high-res product images, MSDS technical PDFs, and specifications from <code>C:\xampp\htdocs\SR</code> into Laravel.
                </p>
            </div>
            <form action="{{ route('admin.products.import-core-php') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn btn-primary font-bold px-4 text-nowrap" onclick="return confirm('Start full Core PHP product, image, PDF, and hierarchy migration from C:\\xampp\\htdocs\\SR?')">
                    <i class="fa-solid fa-rocket me-2"></i> Run Core PHP Migration
                </button>
            </form>
        </div>
    </div>

    <form action="{{ route('admin.products.process-import-hierarchy') }}" method="POST" id="importForm">
        @csrf

        <div class="mb-4">
            <label class="form-label font-semibold text-dark text-14">
                Paste Product Hierarchy (ASCII Tree or Indented Text)
            </label>
            <textarea name="hierarchy_text" id="hierarchyText" rows="18" class="form-control font-monospace text-13 bg-dark text-light p-3 rounded" style="line-height: 1.6;" required placeholder="Paste product tree here...">{{ old('hierarchy_text', $defaultTemplate) }}</textarea>
            <div class="form-text text-12 text-muted mt-2">
                Supported formatting includes ASCII branch symbols (<code>├──</code>, <code>└──</code>, <code>│</code>), tabs, or space indentation. Duplicate categories and products are automatically detected and reused.
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
            <button type="button" id="btnPreview" class="btn btn-outline-primary px-4 py-2 font-semibold">
                <i class="fa-solid fa-eye me-2"></i> Preview Hierarchy Structure
            </button>

            <button type="submit" class="btn btn-brand-primary px-5 py-2 font-bold text-15">
                <i class="fa-solid fa-file-import me-2"></i> Import &amp; Save Hierarchy
            </button>
        </div>
    </form>

    <!-- LIVE PREVIEW CONTAINER -->
    <div id="previewCard" class="card border-primary-subtle d-none">
        <div class="card-header bg-primary-subtle text-primary font-bold d-flex justify-content-between align-items-center py-3">
            <span><i class="fa-solid fa-network-wired me-2"></i> Parsed Hierarchy Tree Preview</span>
            <span id="previewCountBadge" class="badge bg-primary text-white">0 Items Found</span>
        </div>
        <div class="card-body p-3 bg-light">
            <div id="previewList" class="list-group list-group-flush font-monospace text-13"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#btnPreview').click(function() {
        const text = $('#hierarchyText').val().trim();
        if (!text) {
            alert('Please paste hierarchy text first.');
            return;
        }

        const btn = $(this);
        btn.html('<i class="fa-solid fa-spinner fa-spin me-2"></i> Parsing...').prop('disabled', true);

        $.ajax({
            url: "{{ route('admin.products.preview-hierarchy') }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                hierarchy_text: text
            },
            success: function(response) {
                btn.html('<i class="fa-solid fa-eye me-2"></i> Preview Hierarchy Structure').prop('disabled', false);
                if (response.status === 'success') {
                    $('#previewCountBadge').text(response.count + ' Nodes Detected');
                    
                    let html = '';
                    response.nodes.forEach(function(node) {
                        const indent = '&nbsp;&nbsp;&nbsp;&nbsp;'.repeat(node.depth);
                        const isCat = node.type === 'category';
                        const badgeClass = isCat ? 'bg-primary-subtle text-primary border-primary' : 'bg-success-subtle text-success border-success';
                        const icon = isCat ? 'fa-folder-open text-warning' : 'fa-flask text-success';
                        const typeLabel = isCat ? 'CATEGORY' : 'PRODUCT';

                        html += `<div class="list-group-item bg-white border-bottom py-2">
                            ${indent}
                            <i class="fa-solid ${icon} me-2"></i>
                            <strong class="text-dark">${node.name}</strong>
                            <span class="badge ${badgeClass} border ms-2 text-11">${typeLabel}</span>
                            <span class="text-muted text-11 ms-3 float-end d-none d-md-inline">Path: ${node.path}</span>
                        </div>`;
                    });

                    $('#previewList').html(html);
                    $('#previewCard').removeClass('d-none');
                    $('html, body').animate({
                        scrollTop: $('#previewCard').offset().top - 80
                    }, 500);
                }
            },
            error: function() {
                btn.html('<i class="fa-solid fa-eye me-2"></i> Preview Hierarchy Structure').prop('disabled', false);
                alert('Error parsing hierarchy text. Please verify formatting.');
            }
        });
    });
});
</script>
@endpush
