@extends('admin.layouts.admin')

@section('title', 'Import Products from Excel — SRCIL Admin ERP')
@section('page_title', 'Excel Product Migration & Import')

@section('content')
<div class="container-fluid px-0">
    <div class="row g-4">
        <!-- Main Form & Instructions -->
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 pb-3 mb-4 border-bottom">
                        <div>
                            <h4 class="font-bold text-dark mb-1">
                                <i class="fa-solid fa-file-excel text-success me-2"></i> Excel Product Migration & Auto Import
                            </h4>
                            <p class="text-muted text-14 mb-0">
                                Single authoritative source of truth for products, category hierarchy, and asset mapping.
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('admin.products.download-template') }}" class="btn btn-outline-success font-semibold px-3 py-2 d-inline-flex align-items-center gap-2">
                                <i class="fa-solid fa-download"></i> Download Excel Template
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

                    <!-- Guidelines -->
                    <div class="alert alert-info border-0 bg-info-subtle text-dark p-3 rounded-3 mb-4">
                        <div class="font-bold mb-1 text-15"><i class="fa-solid fa-info-circle me-1"></i> Excel Import Rules:</div>
                        <ul class="mb-0 text-13 ps-3">
                            <li><strong>Source of Truth:</strong> Product attributes, category path, and physical asset paths must be defined in Excel.</li>
                            <li><strong>Hierarchy Auto-Creation:</strong> <code>full_category_path</code> (e.g. <em>GACL Products > Acid Products</em>) auto-generates category nodes.</li>
                            <li><strong>Read-Only Source Safety:</strong> Original source files in <code>C:\xampp\htdocs\SR</code> are copied safely into Laravel without deleting or altering source files.</li>
                            <li><strong>Duplicate Handling:</strong> Products matching <code>product_name + full_category_path</code> are updated; new ones are created.</li>
                        </ul>
                    </div>

                    <!-- Upload Form -->
                    <form id="excelImportForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3 align-items-center">
                            <div class="col-md-7">
                                <label for="excel_file" class="form-label font-bold text-dark text-14">Select Excel File (.xlsx, .csv)</label>
                                <input type="file" name="excel_file" id="excel_file" class="form-control form-control-lg @error('excel_file') is-invalid @enderror" accept=".xlsx,.xls,.csv" required>
                                @error('excel_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-5 pt-md-4">
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" role="switch" name="replace_mode" value="1" id="replace_mode">
                                    <label class="form-check-input-label font-semibold text-dark text-13" for="replace_mode">
                                        REPLACE PRODUCT DATA FROM EXCEL
                                        <small class="d-block text-muted">Mark products NOT present in Excel as inactive</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mt-4 pt-3 border-top">
                            <button type="submit" formaction="{{ route('admin.products.validate-excel') }}" class="btn btn-outline-primary font-semibold px-4 py-2">
                                <i class="fa-solid fa-magnifying-glass me-1"></i> Validate Only
                            </button>
                            <button type="submit" formaction="{{ route('admin.products.process-import-excel') }}" class="btn btn-brand-green font-semibold px-4 py-2" onclick="return confirm('Execute Excel import into Database?');">
                                <i class="fa-solid fa-file-import me-1"></i> Validate &amp; Import
                            </button>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-light border font-semibold px-3 py-2 ms-auto">
                                Back to Products
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Validation Preview Report Card -->
            @if(session('validation_report'))
            @php $vReport = session('validation_report'); @endphp
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom p-4">
                    <h5 class="font-bold text-dark mb-0">
                        <i class="fa-solid fa-clipboard-check text-primary me-2"></i> Validation Preview Report
                    </h5>
                </div>
                <div class="card-body p-4">
                    <!-- Stat Cards -->
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-3">
                            <div class="p-3 bg-light rounded-3 text-center border">
                                <div class="text-12 text-muted uppercase font-bold mb-1">Total Rows</div>
                                <div class="text-24 font-extrabold text-dark">{{ $vReport['total_rows'] }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-3 bg-success-subtle rounded-3 text-center border border-success-subtle">
                                <div class="text-12 text-success uppercase font-bold mb-1">Valid Rows</div>
                                <div class="text-24 font-extrabold text-success">{{ $vReport['valid_rows'] }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-3 bg-primary-subtle rounded-3 text-center border border-primary-subtle">
                                <div class="text-12 text-primary uppercase font-bold mb-1">New Products</div>
                                <div class="text-24 font-extrabold text-primary">{{ $vReport['new_products'] }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-3 bg-info-subtle rounded-3 text-center border border-info-subtle">
                                <div class="text-12 text-info uppercase font-bold mb-1">Update Products</div>
                                <div class="text-24 font-extrabold text-info">{{ $vReport['updated_products'] }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Details Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 60px;">Row</th>
                                    <th>Product Name</th>
                                    <th>Category Path</th>
                                    <th>Image</th>
                                    <th>PDF</th>
                                    <th>Action</th>
                                    <th>Status</th>
                                    <th>Message</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($vReport['row_details'] as $row)
                                <tr>
                                    <td class="font-bold text-center">{{ $row['row'] }}</td>
                                    <td class="font-semibold text-dark">{{ $row['product_name'] }}</td>
                                    <td><code class="text-primary">{{ $row['category_path'] }}</code></td>
                                    <td>
                                        <span class="badge {{ $row['image_status'] === 'Found' ? 'bg-success' : 'bg-warning text-dark' }}">
                                            {{ $row['image_status'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $row['pdf_status'] === 'Found' ? 'bg-success' : 'bg-warning text-dark' }}">
                                            {{ $row['pdf_status'] }}
                                        </span>
                                    </td>
                                    <td><span class="badge bg-secondary">{{ $row['action_type'] }}</span></td>
                                    <td>
                                        @if($row['status'] === 'VALID')
                                        <span class="badge bg-success">VALID</span>
                                        @elseif($row['status'] === 'WARNING')
                                        <span class="badge bg-warning text-dark">WARNING</span>
                                        @else
                                        <span class="badge bg-danger">FAILED</span>
                                        @endif
                                    </td>
                                    <td class="text-13">{{ $row['message'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Final Import Execution Report Card -->
            @if(session('import_report'))
            @php $iReport = session('import_report'); @endphp
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-success text-white p-4 rounded-top-4">
                    <h5 class="font-bold mb-0 text-white">
                        <i class="fa-solid fa-circle-check me-2"></i> Import Execution Complete Report
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-2">
                            <div class="p-3 bg-light rounded-3 text-center border">
                                <div class="text-11 text-muted uppercase font-bold mb-1">Source Rows</div>
                                <div class="text-20 font-extrabold text-dark">{{ $iReport['total_rows'] }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-2">
                            <div class="p-3 bg-success-subtle rounded-3 text-center border border-success-subtle">
                                <div class="text-11 text-success uppercase font-bold mb-1">Created</div>
                                <div class="text-20 font-extrabold text-success">{{ $iReport['created_count'] }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-2">
                            <div class="p-3 bg-primary-subtle rounded-3 text-center border border-primary-subtle">
                                <div class="text-11 text-primary uppercase font-bold mb-1">Updated</div>
                                <div class="text-20 font-extrabold text-primary">{{ $iReport['updated_count'] }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-2">
                            <div class="p-3 bg-info-subtle rounded-3 text-center border border-info-subtle">
                                <div class="text-11 text-info uppercase font-bold mb-1">Images Copied</div>
                                <div class="text-20 font-extrabold text-info">{{ $iReport['images_copied'] }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-2">
                            <div class="p-3 bg-info-subtle rounded-3 text-center border border-info-subtle">
                                <div class="text-11 text-info uppercase font-bold mb-1">PDFs Copied</div>
                                <div class="text-20 font-extrabold text-info">{{ $iReport['pdfs_copied'] }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-2">
                            <div class="p-3 bg-warning-subtle rounded-3 text-center border border-warning-subtle">
                                <div class="text-11 text-warning uppercase font-bold mb-1">Failed / Skipped</div>
                                <div class="text-20 font-extrabold text-warning">{{ $iReport['failed_count'] }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Row Level Results Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 60px;">Row</th>
                                    <th>Product Name</th>
                                    <th>Category Path</th>
                                    <th>Image</th>
                                    <th>PDF</th>
                                    <th>Status</th>
                                    <th>Message</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($iReport['row_results'] as $res)
                                <tr>
                                    <td class="font-bold text-center">{{ $res['row'] }}</td>
                                    <td class="font-bold text-dark">{{ $res['product_name'] }}</td>
                                    <td><code class="text-primary">{{ $res['category_path'] }}</code></td>
                                    <td><span class="badge {{ $res['image_status'] === 'Found' ? 'bg-success' : 'bg-secondary' }}">{{ $res['image_status'] }}</span></td>
                                    <td><span class="badge {{ $res['pdf_status'] === 'Found' ? 'bg-success' : 'bg-secondary' }}">{{ $res['pdf_status'] }}</span></td>
                                    <td>
                                        @if($res['status'] === 'CREATED')
                                        <span class="badge bg-success">CREATED</span>
                                        @elseif($res['status'] === 'UPDATED')
                                        <span class="badge bg-primary">UPDATED</span>
                                        @else
                                        <span class="badge bg-danger">{{ $res['status'] }}</span>
                                        @endif
                                    </td>
                                    <td class="text-13">{{ $res['message'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
