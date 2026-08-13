@extends('admin.layouts.admin')

@section('title', 'Media Library — SRCIL Admin ERP')
@section('page_title', 'Product Media Library')

@section('content')
<div class="card-custom p-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="mb-1 text-primary font-bold"><i class="fa-solid fa-photo-film me-2"></i> Media Library</h4>
            <p class="text-muted mb-0">Browse, filter, assign, and manage all product image assets.</p>
        </div>
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <form action="{{ route('admin.products.reconcile-images') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success" onclick="return confirm('Run global reconciliation across all products and local images?')">
                    <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Auto Match All Product Images
                </button>
            </form>
            <form action="{{ route('admin.products.resync-images') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-success" onclick="return confirm('Scan public/assets/products/ and auto-assign matching image files to products?')">
                    <i class="fa-solid fa-rotate me-1"></i> Re-Sync Existing Images
                </button>
            </form>
            <a href="{{ route('admin.products.bulk-pdf') }}" class="btn btn-outline-danger">
                <i class="fa-solid fa-file-pdf me-1"></i> Bulk PDF Auto-Matching
            </a>
            <a href="{{ route('admin.products.bulk-images') }}" class="btn btn-brand-primary">
                <i class="fa-solid fa-cloud-arrow-up me-1"></i> Bulk Image Upload
            </a>
            <a href="{{ route('admin.products.duplicate-images') }}" class="btn btn-outline-warning">
                <i class="fa-solid fa-images me-1"></i> Replace Duplicate Images
            </a>
        </div>
    </div>

    <!-- PDF Assets Badge Bar -->
    <div class="p-3 bg-light rounded border mb-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="font-semibold text-dark text-13">
            <i class="fa-solid fa-folder-open text-primary me-2"></i> <strong>PDF Asset Library:</strong>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 font-semibold text-12">
                <i class="fa-solid fa-flask me-1"></i> MSDS PDFs: <strong>{{ $msdsPdfCount ?? 0 }}</strong>
            </span>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 font-semibold text-12">
                <i class="fa-solid fa-file-contract me-1"></i> Specification PDFs: <strong>{{ $specPdfCount ?? 0 }}</strong>
            </span>
            <span class="badge bg-secondary text-white px-3 py-2 font-semibold text-12">
                <i class="fa-solid fa-file-pdf me-1"></i> Total PDFs: <strong>{{ $totalPdfCount ?? 0 }}</strong>
            </span>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Filters & Search -->
    <form action="{{ route('admin.media.index') }}" method="GET" class="d-flex flex-wrap gap-2 mb-4">
        <div class="search-topbar flex-grow-1" style="max-width: 400px;">
            <i class="fa-solid fa-search"></i>
            <input type="text" name="search" placeholder="Search image filename or product..." value="{{ $search }}">
        </div>

        <select name="filter" class="form-select w-auto" onchange="this.form.submit()">
            <option value="all" {{ $filter === 'all' ? 'selected' : '' }}>All Images</option>
            <option value="assigned" {{ $filter === 'assigned' ? 'selected' : '' }}>Assigned to Products</option>
            <option value="unassigned" {{ $filter === 'unassigned' ? 'selected' : '' }}>Unassigned / Orphan Images</option>
            <option value="duplicate" {{ $filter === 'duplicate' ? 'selected' : '' }}>Assigned to Multiple Products</option>
        </select>

        <button type="submit" class="btn btn-secondary px-3">Filter</button>
        @if($search || $filter !== 'all')
        <a href="{{ route('admin.media.index') }}" class="btn btn-light border px-3">Reset</a>
        @endif
    </form>

    <!-- Media Grid -->
    <div class="row g-3">
        @forelse($mediaItems as $item)
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="card h-100 border shadow-sm">
                <div class="position-relative bg-light text-center p-2 rounded-top" style="height: 180px; overflow: hidden;">
                    <img src="{{ $item['url'] }}" alt="{{ $item['filename'] }}" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                    <span class="position-absolute top-0 end-0 m-2 badge {{ $item['is_duplicate'] ? 'bg-warning text-dark' : ($item['is_assigned'] ? 'bg-success' : 'bg-secondary') }}">
                        {{ $item['is_duplicate'] ? 'Duplicate (' . $item['assigned_count'] . ' prods)' : ($item['is_assigned'] ? 'Assigned' : 'Unassigned') }}
                    </span>
                </div>
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div>
                        <div class="fw-bold text-truncate text-13" title="{{ $item['filename'] }}">{{ $item['filename'] }}</div>
                        <div class="text-muted text-11 mb-2">{{ $item['size_formatted'] }} | {{ strtoupper($item['extension']) }}</div>
                        
                        @if($item['is_assigned'])
                        <div class="text-12 mb-2">
                            <span class="fw-semibold text-dark">Assigned to:</span>
                            <ul class="ps-3 mb-0 text-11 text-muted">
                                @foreach($item['assigned_products'] as $ap)
                                <li><a href="{{ route('admin.products.edit', $ap->id) }}" target="_blank">{{ $ap->name }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                        @else
                        <div class="text-12 text-muted fst-italic mb-2">Not assigned to any product.</div>
                        @endif
                    </div>

                    <div class="pt-2 border-top d-flex gap-1 mt-2">
                        <!-- Quick Assign Form -->
                        <button type="button" class="btn btn-sm btn-outline-primary flex-grow-1" data-bs-toggle="modal" data-bs-target="#assignModal{{ $loop->index }}">
                            <i class="fa-solid fa-link me-1"></i> Assign
                        </button>

                        @if(!$item['is_assigned'])
                        <form action="{{ route('admin.media.destroy') }}" method="POST" onsubmit="return confirm('Delete image file {{ $item['filename'] }}?');">
                            @csrf
                            <input type="hidden" name="image_path" value="{{ $item['relative_path'] }}">
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Unused File">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Assign Modal -->
        <div class="modal fade" id="assignModal{{ $loop->index }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.media.assign') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title font-bold text-15">Assign Image to Product</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-start">
                            <div class="text-center mb-3">
                                <img src="{{ $item['url'] }}" style="height:120px; object-fit:contain;" class="rounded border">
                                <div class="text-muted text-12 mt-1">{{ $item['filename'] }}</div>
                            </div>

                            <input type="hidden" name="image_path" value="{{ $item['relative_path'] }}">

                            <div class="mb-3">
                                <label class="form-label font-semibold">Select Target Product:</label>
                                <select name="product_id" class="form-select" required>
                                    <option value="">-- Choose Product --</option>
                                    @foreach($products as $prod)
                                    <option value="{{ $prod->id }}">{{ $prod->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-brand-primary">Assign Image</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 py-5 text-center text-muted">
            <i class="fa-solid fa-folder-open display-4 mb-3 text-secondary"></i>
            <h5>No Image Files Found</h5>
            <p>No media items match your search or filter options.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
