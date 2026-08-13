@extends('admin.layouts.admin')

@section('title', 'Edit Product — ' . $product->name)
@section('page_title', 'Edit Product: ' . $product->name)

@section('content')
<form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row g-4">
        <!-- Left Column: Primary Specifications & Details -->
        <div class="col-lg-8">
            <!-- Basic Information Card -->
            <div class="card-custom p-4 mb-4">
                <h3 class="h5 font-bold text-dark mb-3"><i class="fa-solid fa-flask text-primary me-2"></i> Basic Product Information</h3>
                
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label font-semibold text-dark text-14">Product Name *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" required value="{{ old('name', $product->name) }}">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-semibold text-dark text-14">Slug (URL Identifier) *</label>
                        <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" required value="{{ old('slug', $product->slug) }}">
                        @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label font-semibold text-dark text-14">Chemical Name / Formula</label>
                        <input type="text" name="chemical_name" class="form-control" value="{{ old('chemical_name', $product->chemical_name) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-semibold text-dark text-14">Brand / Manufacturer</label>
                        <input type="text" name="brand" class="form-control" value="{{ old('brand', $product->brand) }}">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label font-semibold text-dark text-14">CAS Number</label>
                        <input type="text" name="cas_number" class="form-control" value="{{ old('cas_number', $product->cas_number) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label font-semibold text-dark text-14">HSN Code</label>
                        <input type="text" name="hsn_code" class="form-control" value="{{ old('hsn_code', $product->hsn_code) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label font-semibold text-dark text-14">Purity Grade</label>
                        <input type="text" name="purity" class="form-control" value="{{ old('purity', $product->purity) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label font-semibold text-dark text-14">Packaging Type</label>
                        <input type="text" name="packaging" class="form-control" value="{{ old('packaging', $product->packaging) }}">
                    </div>
                </div>

                <div class="mb-0">
                    <label class="form-label font-semibold text-dark text-14">Detailed Description *</label>
                    <textarea name="description" rows="5" class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $product->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <!-- Features & Applications Card -->
            <div class="card-custom p-4 mb-4">
                <h3 class="h5 font-bold text-dark mb-3"><i class="fa-solid fa-list-check text-success me-2"></i> Features &amp; Applications</h3>
                
                <div class="mb-4">
                    <label class="form-label font-semibold text-dark text-14">Key Advantages / Bullet Points</label>
                    <div id="features-container">
                        @php $features = is_array($product->features) ? $product->features : []; @endphp
                        @forelse($features as $f)
                        <div class="input-group mb-2">
                            <input type="text" name="features[]" class="form-control" value="{{ $f }}">
                            <button type="button" class="btn btn-outline-danger remove-row"><i class="fa-solid fa-trash"></i></button>
                        </div>
                        @empty
                        <div class="input-group mb-2">
                            <input type="text" name="features[]" class="form-control" placeholder="Feature point 1">
                            <button type="button" class="btn btn-outline-secondary add-feature"><i class="fa-solid fa-plus"></i></button>
                        </div>
                        @endforelse
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary mt-1 add-feature"><i class="fa-solid fa-plus me-1"></i> Add Feature</button>
                </div>

                <div class="mb-0">
                    <label class="form-label font-semibold text-dark text-14">Major Industrial Applications</label>
                    <div id="applications-container">
                        @php $apps = is_array($product->applications) ? $product->applications : []; @endphp
                        @forelse($apps as $a)
                        <div class="input-group mb-2">
                            <input type="text" name="applications[]" class="form-control" value="{{ $a }}">
                            <button type="button" class="btn btn-outline-danger remove-row"><i class="fa-solid fa-trash"></i></button>
                        </div>
                        @empty
                        <div class="input-group mb-2">
                            <input type="text" name="applications[]" class="form-control" placeholder="Application 1">
                            <button type="button" class="btn btn-outline-secondary add-app"><i class="fa-solid fa-plus"></i></button>
                        </div>
                        @endforelse
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary mt-1 add-app"><i class="fa-solid fa-plus me-1"></i> Add Application</button>
                </div>
            </div>
            </div>
        </div>

        <!-- Right Column: Media, Category & Status Controls -->
        <div class="col-lg-4">
            <!-- Category & Featured Status Card -->
            <div class="card-custom p-4 mb-4">
                <h3 class="h5 font-bold text-dark mb-3"><i class="fa-solid fa-sliders text-warning me-2"></i> Dynamic Hierarchy &amp; Visibility</h3>

                <div class="alert alert-light border py-2 text-13 mb-3">
                    <i class="fa-solid fa-sitemap text-primary me-1"></i> Current Location: <br>
                    <strong>{{ $product->hierarchy_path }}</strong>
                </div>

                <div class="mb-3">
                    <label class="form-label font-semibold text-dark text-14">Change Hierarchy Path</label>
                    <input type="text" name="category_path" class="form-control" value="{{ old('category_path') }}" placeholder="e.g. GNFC Products > Organic Acids & Esters">
                    <div class="form-text text-12 text-primary">
                        <i class="fa-solid fa-magic me-1"></i> Enter new path to relocate product (e.g. <code>GNFC Products > Organic Acids & Esters</code>). Missing categories will be auto-created without duplicates!
                    </div>
                </div>
                


                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label font-semibold text-dark text-14">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $product->sort_order) }}" placeholder="0">
                    </div>
                    <div class="col-6">
                        <label class="form-label font-semibold text-dark text-14">Status</label>
                        <div class="form-check form-switch pt-2">
                            <input class="form-check-input" type="checkbox" name="status" value="1" id="prodStatus" {{ old('status', $product->status) ? 'checked' : '' }}>
                            <label class="form-check-label font-semibold text-13" for="prodStatus">Active</label>
                        </div>
                    </div>
                </div>

                <div class="form-check form-switch p-3 bg-light rounded-3 border">
                    <input class="form-check-input ms-0 me-2" type="checkbox" name="is_featured" value="1" id="is_featured" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                    <label class="form-check-label font-semibold text-dark text-14" for="is_featured">
                        Featured on Homepage
                    </label>
                </div>
            </div>

            <!-- Media Uploads Card -->
            <div class="card-custom p-4 mb-4">
                <h3 class="h5 font-bold text-dark mb-3"><i class="fa-solid fa-image text-danger me-2"></i> Product Media, Specification &amp; MSDS</h3>
                
                <div class="mb-4">
                    <label class="form-label font-semibold text-dark text-14">Product Image</label>
                    
                    @if($product->image_url)
                    <div class="mb-3 text-center p-2 bg-light rounded border" id="imagePreviewWrap">
                        <img src="{{ asset($product->image_url) }}" id="imagePreview" class="img-fluid rounded border max-h-180 mb-2" alt="{{ $product->name }}">
                        <div class="text-12 font-semibold text-dark">{{ basename($product->image_url) }}</div>
                        <div class="text-11 text-muted">Path: <code>{{ $product->image_url }}</code></div>
                    </div>
                    @endif

                    <input type="file" name="image" class="form-control mb-2" accept="image/jpeg,image/png,image/webp,image/gif" id="imageInput">
                    
                    <a href="{{ route('admin.media.index') }}" target="_blank" class="btn btn-sm btn-outline-secondary w-100 text-12">
                        <i class="fa-solid fa-photo-film me-1"></i> Choose from Media Library
                    </a>
                </div>

                <!-- Specification Document / File Field -->
                <div class="mb-4">
                    <label class="form-label font-semibold text-dark text-14">Specification File / Document (PDF, JPG, PNG)</label>
                    
                    @if($product->spec_pdf_url)
                    @php 
                        $specPath = $product->spec_pdf_url;
                        $specFileName = basename($specPath);
                    @endphp
                    <div class="mb-3 p-3 bg-light rounded border" id="specImagePreviewWrap">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div>
                                <div class="font-semibold text-dark text-13"><i class="fa-solid fa-file-pdf text-danger me-1"></i> Current File: <code>assets/pdf/Specification/{{ $specFileName }}</code></div>
                            </div>
                            <a href="{{ asset('assets/pdf/Specification/' . $specFileName) }}" target="_blank" class="btn btn-sm btn-outline-primary font-semibold text-12">
                                <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> View Specification PDF
                            </a>
                        </div>
                        <div class="form-check form-switch text-start pt-1">
                            <input class="form-check-input me-2" type="checkbox" name="remove_specification_image" value="1" id="removeSpecImg">
                            <label class="form-check-label text-12 text-danger font-semibold" for="removeSpecImg">Remove Specification File</label>
                        </div>
                    </div>
                    @else
                    <div class="mb-2 text-13 text-muted">No specification file uploaded yet.</div>
                    @endif

                    <input type="file" name="specification_image" class="form-control @error('specification_image') is-invalid @enderror" accept=".pdf,image/jpeg,image/png,image/webp" id="specImageInput">
                    @error('specification_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <!-- MSDS / Certificate File Field -->
                <div class="mb-0">
                    <label class="form-label font-semibold text-dark text-14">MSDS / Certificate File (PDF)</label>
                    
                    @if($product->msds_pdf_url)
                    @php 
                        $msdsPath = $product->msds_pdf_url;
                        $msdsFileName = basename($msdsPath);
                    @endphp
                    <div class="mb-2 p-3 bg-light rounded border text-13">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div>
                                <div class="font-semibold text-dark text-13"><i class="fa-solid fa-file-pdf text-danger me-1"></i> Current File: <code>assets/pdf/MSDC/{{ $msdsFileName }}</code></div>
                            </div>
                            <a href="{{ asset('assets/pdf/MSDC/' . $msdsFileName) }}" target="_blank" class="btn btn-sm btn-outline-primary font-semibold text-12">
                                <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> View MSDS PDF
                            </a>
                        </div>
                        <div class="form-check form-switch d-inline-block">
                            <input class="form-check-input me-2" type="checkbox" name="remove_msds" value="1" id="removeMsds">
                            <label class="form-check-label text-12 text-danger font-semibold" for="removeMsds">Remove MSDS / Certificate File</label>
                        </div>
                    </div>
                    @else
                    <div class="mb-2 text-13 text-muted">No MSDS / Certificate file uploaded yet.</div>
                    @endif

                    <input type="file" name="msds" class="form-control @error('msds') is-invalid @enderror" accept=".pdf,image/jpeg,image/png,image/webp">
                    @error('msds')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <!-- Submit Action Card -->
            <div class="card-custom p-4">
                <button type="submit" class="btn btn-brand-primary w-100 py-3 font-bold mb-2">
                    <i class="fa-solid fa-save me-2"></i> Update Product
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
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endpush
