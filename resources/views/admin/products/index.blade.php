@extends('admin.layouts.admin')

@section('title', 'Product Catalog Management — SRCIL Admin ERP')
@section('page_title', 'Chemical Products Catalog')

@section('content')
<div class="card-custom p-4">
    <!-- Header Controls & Filters -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <form action="{{ route('admin.products.index') }}" method="GET" class="d-flex flex-wrap gap-2 flex-grow-1 max-w-700">
            <div class="search-topbar flex-grow-1" style="width: auto;">
                <i class="fa-solid fa-search"></i>
                <input type="text" name="search" placeholder="Search product name, chemical formula, CAS#, HSN..." value="{{ request('search') }}">
            </div>
            
            <select name="category_id" class="form-select w-auto bg-light border-secondary-subtle">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            
            <button type="submit" class="btn btn-brand-primary px-3">
                <i class="fa-solid fa-filter me-1"></i> Filter
            </button>
            
            @if(request('search') || request('category_id'))
            <a href="{{ route('admin.products.index') }}" class="btn btn-light border px-3" title="Reset Filters">
                <i class="fa-solid fa-rotate-right"></i>
            </a>
            @endif
        </form>

        <div class="d-flex flex-wrap gap-2 align-items-center">
            <button type="button" class="btn btn-outline-danger font-semibold d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#deleteAllImagesModal">
                <i class="fa-solid fa-trash-can"></i> Delete All Product Images
            </button>
            <a href="{{ route('admin.products.bulk-auto-update') }}" class="btn btn-warning font-bold d-inline-flex align-items-center gap-2 text-dark shadow-sm">
                <i class="fa-solid fa-bolt"></i> Bulk Update All Products
            </a>
            <a href="{{ route('admin.products.import-excel') }}" class="btn btn-outline-success d-inline-flex align-items-center gap-2">
                <i class="fa-solid fa-file-excel"></i> Import Products from Excel
            </a>
            <a href="{{ route('admin.products.create') }}" class="btn btn-brand-green d-inline-flex align-items-center gap-2">
                <i class="fa-solid fa-plus"></i> Add New Product
            </a>
        </div>
    </div>

    <!-- Modal: Delete All Product Images Confirmation -->
    <div class="modal fade" id="deleteAllImagesModal" tabindex="-1" aria-labelledby="deleteAllImagesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-bold" id="deleteAllImagesModalLabel">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i> Delete All Product Images
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-warning border-start border-4 border-warning mb-3">
                        <p class="font-bold text-dark mb-1"><i class="fa-solid fa-triangle-exclamation text-warning me-1"></i> Strong Confirmation Required</p>
                        <p class="text-13 text-dark mb-0">
                            Are you sure you want to remove all product images?<br>
                            This will remove image assignments and delete the corresponding stored image files.<br>
                            <strong>Products themselves will NOT be deleted.</strong>
                        </p>
                    </div>
                    <ul class="text-13 text-muted mb-0">
                        <li><i class="fa-solid fa-check text-success me-1"></i> Database product records remain 100% intact.</li>
                        <li><i class="fa-solid fa-check text-success me-1"></i> Categories, names, and descriptions are NOT modified.</li>
                        <li><i class="fa-solid fa-check text-success me-1"></i> MSDS and Specification PDF files are NOT deleted.</li>
                    </ul>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary font-semibold" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('admin.products.delete-all-images') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger font-bold">
                            <i class="fa-solid fa-trash-can me-1"></i> Yes, Remove All Product Images
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Action Toolbar -->
    <form action="{{ route('admin.products.bulk-update') }}" method="POST" id="bulkActionForm">
        @csrf
        <div id="bulkActionBar" class="alert alert-warning py-2 px-3 mb-3 d-none align-items-center justify-content-between flex-wrap gap-2">
            <div class="font-semibold text-13 d-flex align-items-center gap-2">
                <i class="fa-solid fa-check-square"></i> <span id="selectedCount">0</span> products selected
            </div>
            
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <select name="bulk_action" id="bulkActionSelect" class="form-select form-select-sm w-auto font-semibold">
                    <option value="">-- Choose Bulk Action --</option>
                    <option value="activate">Activate Selected</option>
                    <option value="deactivate">Deactivate Selected</option>
                    <option value="set_brand">Set Brand</option>
                    <option value="set_purity">Set Purity</option>
                    <option value="set_packaging">Set Packaging</option>
                    <option value="delete">Delete Selected</option>
                </select>

                <input type="text" name="bulk_brand" id="bulkBrandInput" class="form-control form-control-sm w-auto d-none" placeholder="Enter Brand Name (e.g. GACL)">
                <input type="text" name="bulk_purity" id="bulkPurityInput" class="form-control form-control-sm w-auto d-none" placeholder="Enter Purity (e.g. 99% Min)">
                <input type="text" name="bulk_packaging" id="bulkPackagingInput" class="form-control form-control-sm w-auto d-none" placeholder="Enter Packaging">

                <button type="submit" class="btn btn-primary btn-sm px-3 font-semibold" onclick="return confirm('Apply selected bulk update?');">
                    <i class="fa-solid fa-bolt me-1"></i> Apply Bulk Update
                </button>
            </div>
        </div>

        <!-- Products Table -->
        <div class="table-responsive">
            <table class="table table-custom align-middle">
                <thead>
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" id="selectAll" class="form-check-input">
                        </th>
                        <th>Image</th>
                        <th>Product Name</th>
                        <th>Brand</th>
                        <th>Category</th>
                        <th>HSN Code</th>
                        <th>Packaging</th>
                        <th>Purity</th>
                        <th>Application</th>
                        <th>Featured</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $p)
                    <tr>
                        <td>
                            <input type="checkbox" name="ids[]" value="{{ $p->id }}" class="form-check-input product-checkbox">
                        </td>
                        <td style="width: 50px;">
                            <img src="{{ asset($p->image_url) }}" alt="{{ $p->name }}" width="42" height="42" class="rounded-3 object-fit-cover border shadow-sm">
                        </td>
                        <td>
                            <div class="font-bold text-dark text-14">{{ $p->name }}</div>
                            <div class="text-12 text-muted">{{ $p->chemical_name ?? '' }}</div>
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-dark border px-2 py-1 text-12 font-semibold">
                                {{ $p->brand ?? 'SRCIL Standard' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-light text-primary border px-2 py-1 text-12 font-semibold">
                                {{ $p->category ? $p->category->name : 'N/A' }}
                            </span>
                        </td>
                        <td><code class="text-dark font-semibold text-12">{{ $p->hsn_code ?? 'N/A' }}</code></td>
                        <td><span class="text-12 text-dark font-semibold">{{ $p->packaging ?? 'Standard' }}</span></td>
                        <td><span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 text-12">{{ $p->purity ?? 'Standard' }}</span></td>
                        <td>
                            <div class="text-12 text-muted text-truncate" style="max-width: 160px;" title="{{ is_array($p->applications) ? implode(', ', $p->applications) : ($p->applications ?? 'Industrial Application') }}">
                                {{ is_array($p->applications) ? implode(', ', $p->applications) : ($p->applications ?? 'Industrial Application') }}
                            </div>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm border-0 bg-transparent btn-toggle-featured" data-url="{{ route('admin.products.toggle-featured', $p->id) }}" title="Toggle Featured Homepage Display">
                                @if($p->is_featured)
                                    <i class="fa-solid fa-star text-warning text-16" title="Featured Item"></i>
                                @else
                                    <i class="fa-regular fa-star text-muted text-16" title="Click to Feature"></i>
                                @endif
                            </button>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('products.show', $p->slug) }}" target="_blank" class="btn btn-sm btn-light border text-secondary" title="View Public Page">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.products.edit', $p->id) }}" class="btn btn-sm btn-light border text-primary" title="Edit Product">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-light border text-danger btn-delete-single" data-action="{{ route('admin.products.destroy', $p->id) }}" title="Delete Product">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-box-open text-32 mb-2 d-block"></i>
                            No chemical products found matching your search criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>

    <!-- Hidden Form for Single Deletion -->
    <form id="singleDeleteForm" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>

    <!-- Hidden Form for Toggle Featured -->
    <form id="toggleFeaturedForm" method="POST" class="d-none">
        @csrf
    </form>

    <!-- Pagination -->
    <div class="mt-4 d-flex justify-content-center">
        {{ $products->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Select All Checkbox Handler
    $('#selectAll').change(function() {
        $('.product-checkbox').prop('checked', $(this).is(':checked'));
        updateBulkBar();
    });

    $('.product-checkbox').change(function() {
        updateBulkBar();
    });

    function updateBulkBar() {
        const checkedCount = $('.product-checkbox:checked').length;
        if (checkedCount > 0) {
            $('#selectedCount').text(checkedCount);
            $('#bulkActionBar').removeClass('d-none').addClass('d-flex');
        } else {
            $('#bulkActionBar').addClass('d-none').removeClass('d-flex');
        }
    }

    // Bulk Action Select Toggle Inputs
    $('#bulkActionSelect').change(function() {
        const val = $(this).val();
        $('#bulkBrandInput').toggleClass('d-none', val !== 'set_brand');
        $('#bulkPurityInput').toggleClass('d-none', val !== 'set_purity');
        $('#bulkPackagingInput').toggleClass('d-none', val !== 'set_packaging');
    });

    // Single Delete Button Handler
    $('.btn-delete-single').click(function() {
        if (confirm('Are you sure you want to delete this product?')) {
            const form = $('#singleDeleteForm');
            form.attr('action', $(this).data('action'));
            form.submit();
        }
    });

    // Toggle Featured Handler
    $('.btn-toggle-featured').click(function() {
        const form = $('#toggleFeaturedForm');
        form.attr('action', $(this).data('url'));
        form.submit();
    });
});
</script>
@endpush
