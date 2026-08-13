@extends('admin.layouts.admin')

@section('title', 'Bulk Update All Products — SRCIL Admin ERP')
@section('page_title', 'Bulk Product Auto-Update')

@section('content')
<div class="container-fluid px-0">
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 pb-3 mb-4 border-bottom">
                <div>
                    <h4 class="font-bold text-dark mb-1">
                        <i class="fa-solid fa-bolt text-warning me-2"></i> Bulk Update All Database Products
                    </h4>
                    <p class="text-muted text-14 mb-0">
                        Process and update standard chemical attributes for ALL products across the entire database in one atomic operation.
                    </p>
                </div>
                <div>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-light border font-semibold px-3 py-2">
                        <i class="fa-solid fa-arrow-left me-1"></i> Back to Products
                    </a>
                </div>
            </div>

            <!-- Flash messages -->
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show font-semibold mb-4" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show font-semibold mb-4" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <!-- Executive Summary Cards -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-2-4">
                    <div class="p-3 bg-light rounded-3 text-center border">
                        <div class="text-12 text-muted uppercase font-bold mb-1">Total Products</div>
                        <div class="text-28 font-extrabold text-dark">{{ $previewReport['total_products'] }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-2-4">
                    <div class="p-3 bg-success-subtle rounded-3 text-center border border-success-subtle">
                        <div class="text-12 text-success uppercase font-bold mb-1">Matched</div>
                        <div class="text-28 font-extrabold text-success">{{ $previewReport['matched_count'] }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-2-4">
                    <div class="p-3 bg-primary-subtle rounded-3 text-center border border-primary-subtle">
                        <div class="text-12 text-primary uppercase font-bold mb-1">To Update</div>
                        <div class="text-28 font-extrabold text-primary">{{ $previewReport['updated_count'] }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-2-4">
                    <div class="p-3 bg-warning-subtle rounded-3 text-center border border-warning-subtle">
                        <div class="text-12 text-warning-emphasis uppercase font-bold mb-1">Unmatched</div>
                        <div class="text-28 font-extrabold text-warning-emphasis">{{ $previewReport['unmatched_count'] }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-2-4">
                    <div class="p-3 bg-danger-subtle rounded-3 text-center border border-danger-subtle">
                        <div class="text-12 text-danger uppercase font-bold mb-1">Failed</div>
                        <div class="text-28 font-extrabold text-danger">{{ $previewReport['failed_count'] }}</div>
                    </div>
                </div>
            </div>

            <!-- Confirmation & Execution Form -->
            <div class="card border-0 bg-light p-3 rounded-4 mb-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div>
                        <div class="font-bold text-dark text-16"><i class="fa-solid fa-triangle-exclamation text-warning me-1"></i> Ready for One-Click Database Update</div>
                        <div class="text-13 text-muted">Clicking the button below will process all {{ $previewReport['total_products'] }} products inside a safe database transaction.</div>
                    </div>
                    <form action="{{ route('admin.products.process-bulk-auto-update') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-warning font-extrabold px-4 py-2 text-dark shadow-sm" onclick="return confirm('Execute Bulk Update for ALL {{ $previewReport['total_products'] }} products across the database?');">
                            <i class="fa-solid fa-bolt me-2"></i> Update ALL {{ $previewReport['total_products'] }} Products Now
                        </button>
                    </form>
                </div>
            </div>

            <!-- Full Preview Table -->
            <h5 class="font-bold text-dark mb-3">
                <i class="fa-solid fa-eye me-1 text-primary"></i> Product Update Preview ({{ count($previewReport['preview_rows']) }} Items)
            </h5>

            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Product Name</th>
                            <th>Category Path</th>
                            <th>Brand</th>
                            <th>HSN Code</th>
                            <th>Packaging</th>
                            <th>Purity</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($previewReport['preview_rows'] as $index => $row)
                        <tr>
                            <td class="font-bold text-center">{{ $index + 1 }}</td>
                            <td class="font-bold text-dark">{{ $row['name'] }}</td>
                            <td><code class="text-primary text-12">{{ $row['category_path'] }}</code></td>
                            <td>
                                <span class="badge bg-secondary-subtle text-dark border px-2 py-1">{{ $row['proposed_brand'] }}</span>
                            </td>
                            <td><code class="text-dark font-bold">{{ $row['proposed_hsn'] }}</code></td>
                            <td><span class="text-12 font-semibold">{{ $row['proposed_packaging'] }}</span></td>
                            <td><span class="badge bg-success-subtle text-success border px-2 py-1">{{ $row['proposed_purity'] }}</span></td>
                            <td>
                                <span class="badge bg-success text-white px-2 py-1 text-12 font-bold">MATCHED</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
