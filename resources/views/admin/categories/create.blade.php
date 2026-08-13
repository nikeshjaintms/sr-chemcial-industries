@extends('admin.layouts.admin')

@section('title', 'Add Category — SRCIL Admin ERP')
@section('page_title', 'Create Product Category')

@section('content')
<div class="card-custom p-4 max-w-700">
    <h3 class="h5 font-bold text-dark mb-4"><i class="fa-solid fa-layer-group text-primary me-2"></i> Category Details</h3>

    @if($parentCategory)
        <div class="alert alert-info py-2 text-13 mb-3">
            <i class="fa-solid fa-sitemap me-1"></i> Creating Child Category under: <strong>{{ $parentCategory->name }}</strong> (Path: {{ $parentCategory->path }})
        </div>
    @endif

    <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label class="form-label font-semibold text-dark text-14">Category Name *</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" required value="{{ old('name') }}" placeholder="e.g. Acid Products">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label font-semibold text-dark text-14">Parent Category (Optional)</label>
            <select name="parent_id" class="form-select">
                <option value="">-- None (Make Root Category) --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('parent_id', $parentId) == $cat->id ? 'selected' : '' }}>
                        {{ $cat->path }}
                    </option>
                @endforeach
            </select>
            <div class="form-text text-12">Select parent category to create subcategory or sub-subcategory. Leave blank for main root category.</div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label font-semibold text-dark text-14">Slug (URL Identifier)</label>
                <input type="text" name="slug" class="form-control" value="{{ old('slug') }}" placeholder="Auto-generated if left blank">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label font-semibold text-dark text-14">Category Type</label>
                <input type="text" name="type" class="form-control" value="{{ old('type', 'Industrial Chemicals') }}" placeholder="e.g. Industrial Chemicals">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label font-semibold text-dark text-14">Sort Order</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}" placeholder="0">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label font-semibold text-dark text-14">Status</label>
                <div class="form-check form-switch pt-2">
                    <input class="form-check-input" type="checkbox" name="status" value="1" id="statusSwitch" {{ old('status', '1') ? 'checked' : '' }}>
                    <label class="form-check-label font-semibold text-14" for="statusSwitch">Enabled (Visible in Menu)</label>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label font-semibold text-dark text-14">Description</label>
            <textarea name="description" rows="3" class="form-control" placeholder="Short description of products in this category...">{{ old('description') }}</textarea>
        </div>

        <div class="mb-4">
            <label class="form-label font-semibold text-dark text-14">Category Banner Image</label>
            <input type="file" name="image" class="form-control" accept="image/*">
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-brand-primary px-4 font-semibold">Save Category</button>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
        </div>
    </form>
</div>
@endsection

