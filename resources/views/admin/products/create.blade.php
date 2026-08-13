@extends('admin.layouts.admin')

@section('title', 'Add New Product — SRCIL Admin ERP')
@section('page_title', 'Create New Chemical Product')

@section('content')
<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="row g-4">
        <!-- Left Column: Primary Specifications & Details -->
        <div class="col-lg-8">
            <!-- Basic Information Card -->
            <div class="card-custom p-4 mb-4">
                <h3 class="h5 font-bold text-dark mb-3"><i class="fa-solid fa-flask text-primary me-2"></i> Basic Product Information</h3>
                
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label font-semibold text-dark text-14">Product Name *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" required value="{{ old('name') }}" placeholder="e.g. Formic Acid 85%">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-semibold text-dark text-14">Slug (URL Identifier)</label>
                        <input type="text" name="slug" class="form-control" value="{{ old('slug') }}" placeholder="Auto-generated if left blank">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label font-semibold text-dark text-14">Chemical Name / Formula</label>
                        <input type="text" name="chemical_name" class="form-control" value="{{ old('chemical_name') }}" placeholder="e.g. Methanoic Acid (HCOOH)">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-semibold text-dark text-14">Brand / Manufacturer</label>
                        <input type="text" name="brand" class="form-control" value="{{ old('brand') }}" placeholder="e.g. GNFC / GACL / SRCIL">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label font-semibold text-dark text-14">CAS Number</label>
                        <input type="text" name="cas_number" class="form-control" value="{{ old('cas_number') }}" placeholder="e.g. 64-18-6">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label font-semibold text-dark text-14">HSN Code</label>
                        <input type="text" name="hsn_code" class="form-control" value="{{ old('hsn_code') }}" placeholder="e.g. 29151100">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label font-semibold text-dark text-14">Purity Grade</label>
                        <input type="text" name="purity" class="form-control" value="{{ old('purity') }}" placeholder="e.g. 85.0% Min Purity">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label font-semibold text-dark text-14">Packaging Type</label>
                        <input type="text" name="packaging" class="form-control" value="{{ old('packaging') }}" placeholder="e.g. 35 Kg HDPE Can">
                    </div>
                </div>

                <div class="mb-0">
                    <label class="form-label font-semibold text-dark text-14">Detailed Description *</label>
                    <textarea name="description" rows="5" class="form-control @error('description') is-invalid @enderror" required placeholder="Comprehensive technical description of the chemical product...">{{ old('description') }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <!-- Features & Applications Card -->
            <div class="card-custom p-4 mb-4">
                <h3 class="h5 font-bold text-dark mb-3"><i class="fa-solid fa-list-check text-success me-2"></i> Features &amp; Applications</h3>
                
                <div class="mb-4">
                    <label class="form-label font-semibold text-dark text-14">Key Advantages / Bullet Points</label>
                    <div id="features-container">
                        <div class="input-group mb-2">
                            <input type="text" name="features[]" class="form-control" placeholder="Feature point 1">
                            <button type="button" class="btn btn-outline-secondary add-feature"><i class="fa-solid fa-plus"></i></button>
                        </div>
                    </div>
                </div>

                <div class="mb-0">
                    <label class="form-label font-semibold text-dark text-14">Major Industrial Applications</label>
                    <div id="applications-container">
                        <div class="input-group mb-2">
                            <input type="text" name="applications[]" class="form-control" placeholder="Application 1 (e.g. Textile processing)">
                            <button type="button" class="btn btn-outline-secondary add-app"><i class="fa-solid fa-plus"></i></button>
                        </div>
                    </div>
                </div>
        </div>

        <!-- Right Column: Media, Category & Status Controls -->
        <div class="col-lg-4">
            <!-- Category & Featured Status Card -->
            <div class="card-custom p-4 mb-4">
                <h3 class="h5 font-bold text-dark mb-3"><i class="fa-solid fa-sliders text-warning me-2"></i> Dynamic Hierarchy &amp; Visibility</h3>
                
                <div class="mb-3">
                    <label class="form-label font-semibold text-dark text-14">Category Hierarchy Path</label>
                    <input type="text" name="category_path" class="form-control" value="{{ old('category_path') }}" placeholder="e.g. Products > GACL Products > Acid Products">
                    <div class="form-text text-12 text-primary">
                        <i class="fa-solid fa-magic me-1"></i> Type hierarchy path (e.g. <code>Products > GACL Products > Acid Products</code>). If a category doesn't exist, it will be automatically created without duplicates!
                    </div>
                </div>



                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label font-semibold text-dark text-14">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}" placeholder="0">
                    </div>
                    <div class="col-6">
                        <label class="form-label font-semibold text-dark text-14">Status</label>
                        <div class="form-check form-switch pt-2">
                            <input class="form-check-input" type="checkbox" name="status" value="1" id="prodStatus" {{ old('status', '1') ? 'checked' : '' }}>
                            <label class="form-check-label font-semibold text-13" for="prodStatus">Active</label>
                        </div>
                    </div>
                </div>

                <div class="form-check form-switch p-3 bg-light rounded-3 border">
                    <input class="form-check-input ms-0 me-2" type="checkbox" name="is_featured" value="1" id="is_featured" {{ old('is_featured') ? 'checked' : '' }}>
                    <label class="form-check-label font-semibold text-dark text-14" for="is_featured">
                        Featured on Homepage
                    </label>
                </div>
            </div>

            <!-- Media Uploads Card -->
            <div class="card-custom p-4 mb-4">
                <h3 class="h5 font-bold text-dark mb-3"><i class="fa-solid fa-image text-danger me-2"></i> Product Media, Specification &amp; MSDS</h3>
                
                <div class="mb-3">
                    <label class="form-label font-semibold text-dark text-14">Product Image</label>
                    <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/webp,image/gif" id="imageInput">
                    <div class="mt-2 text-center d-none" id="imagePreviewWrap">
                        <img src="" id="imagePreview" class="img-fluid rounded-3 border max-h-180">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label font-semibold text-dark text-14">Specification Image (JPG, PNG, WEBP)</label>
                    <input type="file" name="specification_image" class="form-control @error('specification_image') is-invalid @enderror" accept="image/jpeg,image/png,image/webp" id="specImageInput">
                    @error('specification_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="mt-2 text-center d-none" id="specImagePreviewWrap">
                        <img src="" id="specImagePreview" class="img-fluid rounded-3 border max-h-180">
                    </div>
                </div>

                <div class="mb-0">
                    <label class="form-label font-semibold text-dark text-14">MSDS / Certificate File (PDF, JPG, PNG, WEBP)</label>
                    <input type="file" name="msds" class="form-control @error('msds') is-invalid @enderror" accept=".pdf,image/jpeg,image/png,image/webp">
                    @error('msds')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <!-- Submit Action Card -->
            <div class="card-custom p-4">
                <button type="submit" class="btn btn-brand-primary w-100 py-3 font-bold mb-2">
                    <i class="fa-solid fa-save me-2"></i> Save Product
                </button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary w-100 py-2">
                    Cancel
                </a>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.add-feature').click(function() {
        $('#features-container').append('<div class="input-group mb-2"><input type="text" name="features[]" class="form-control" placeholder="Additional feature"><button type="button" class="btn btn-outline-danger remove-row"><i class="fa-solid fa-trash"></i></button></div>');
    });
    $('.add-app').click(function() {
        $('#applications-container').append('<div class="input-group mb-2"><input type="text" name="applications[]" class="form-control" placeholder="Additional application"><button type="button" class="btn btn-outline-danger remove-row"><i class="fa-solid fa-trash"></i></button></div>');
    });
    $(document).on('click', '.remove-row', function() {
        $(this).closest('.input-group, .row').remove();
    });

    $('#imageInput').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').attr('src', e.target.result);
                $('#imagePreviewWrap').removeClass('d-none');
            };
            reader.readAsDataURL(file);
        }
    });

    $('#specImageInput').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#specImagePreview').attr('src', e.target.result);
                $('#specImagePreviewWrap').removeClass('d-none');
            };
            reader.readAsDataURL(file);
    });
});
</script>
@endpush
