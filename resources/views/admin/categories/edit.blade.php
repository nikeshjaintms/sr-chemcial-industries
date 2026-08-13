@extends('admin.layouts.admin')

@section('title', 'Edit Category — ' . $category->name)
@section('page_title', 'Edit Category: ' . $category->name)

@section('content')
<div class="card-custom p-4 max-w-700">
    <h3 class="h5 font-bold text-dark mb-4"><i class="fa-solid fa-layer-group text-primary me-2"></i> Category Details</h3>

    <div class="alert alert-light border py-2 text-13 mb-3">
        <i class="fa-solid fa-sitemap text-primary me-1"></i> Current Hierarchy Path: <strong>{{ $category->path }}</strong>
    </div>

    <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label font-semibold text-dark text-14">Category Name *</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" required value="{{ old('name', $category->name) }}">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label font-semibold text-dark text-14">Parent Category</label>
            <select name="parent_id" class="form-select">
                <option value="">-- None (Make Root Category) --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('parent_id', $category->parent_id) == $cat->id ? 'selected' : '' }}>
                        {{ $cat->path }}
                    </option>
                @endforeach
            </select>
            <div class="form-text text-12">Change parent category to relocate this entire branch in the website hierarchy.</div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label font-semibold text-dark text-14">Slug (URL Identifier) *</label>
                <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" required value="{{ old('slug', $category->slug) }}">
                @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label font-semibold text-dark text-14">Category Type *</label>
                <input type="text" name="type" class="form-control" required value="{{ old('type', $category->type) }}">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label font-semibold text-dark text-14">Sort Order</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $category->sort_order) }}">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label font-semibold text-dark text-14">Status</label>
                <div class="form-check form-switch pt-2">
                    <input class="form-check-input" type="checkbox" name="status" value="1" id="statusSwitch" {{ old('status', $category->status) ? 'checked' : '' }}>
                    <label class="form-check-label font-semibold text-14" for="statusSwitch">Enabled (Visible in Menu)</label>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label font-semibold text-dark text-14">Description</label>
            <textarea name="description" rows="3" class="form-control">{{ old('description', $category->description) }}</textarea>
        </div>

        <div class="mb-4">
            <label class="form-label font-semibold text-dark text-14">Category Banner Image</label>
            <input type="file" name="image" class="form-control" accept="image/*">
            @if($category->image_url)
            <div class="mt-2 text-13 text-muted">Current Image: <code>{{ $category->image_url }}</code></div>
            @endif
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-brand-primary px-4 font-semibold">Update Category</button>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
        </div>
    </form>
</div>
@endsection
