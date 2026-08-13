@extends('admin.layouts.admin')

@section('title', 'Category Management — SRCIL Admin ERP')
@section('page_title', 'Product Categories & Dynamic Hierarchy')

@section('content')
<div class="card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="h5 font-bold text-dark mb-1">Product Category & Hierarchy Management</h2>
            <p class="text-13 text-muted mb-0">Unlimited dynamic levels. Any modifications automatically update website menus, product routes, and chatbot search.</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-brand-green d-inline-flex align-items-center gap-2">
            <i class="fa-solid fa-plus"></i> Add New Category
        </a>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs mb-4" id="categoryTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active font-bold text-14" id="tree-tab" data-bs-toggle="tab" data-bs-target="#tree-pane" type="button" role="tab">
                <i class="fa-solid fa-sitemap text-primary me-2"></i>Live Hierarchy Tree Preview
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link font-bold text-14" id="table-tab" data-bs-toggle="tab" data-bs-target="#table-pane" type="button" role="tab">
                <i class="fa-solid fa-list text-secondary me-2"></i>All Categories Table ({{ $allCategories->count() }})
            </button>
        </li>
    </ul>

    <div class="tab-content" id="categoryTabContent">
        <!-- TREE PREVIEW TAB -->
        <div class="tab-pane fade show active" id="tree-pane" role="tabpanel">
            <div class="bg-white p-3 border rounded">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                    <span class="font-semibold text-dark text-14"><i class="fa-solid fa-network-wired text-primary me-2"></i>Root Menu Structure</span>
                    <span class="text-12 text-muted">Root -> Main Category -> Sub Category -> Sub Sub Category -> Product</span>
                </div>
                <ul class="list-group list-group-flush">
                    @forelse($rootCategories as $root)
                        @include('admin.categories.tree-item', ['category' => $root])
                    @empty
                        <li class="list-group-item text-center py-4 text-muted">No root categories found. Create a category to start building your hierarchy.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- TABLE TAB -->
        <div class="tab-pane fade" id="table-pane" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-custom align-middle">
                    <thead>
                        <tr>
                            <th>Category Name</th>
                            <th>Parent Category</th>
                            <th>Slug</th>
                            <th>Sort Order</th>
                            <th>Status</th>
                            <th>Total Products</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allCategories as $cat)
                        <tr>
                            <td>
                                <div class="font-bold text-dark text-15">{{ $cat->name }}</div>
                                <div class="text-12 text-muted">{{ Str::limit($cat->description, 50) }}</div>
                            </td>
                            <td>
                                @if($cat->parent)
                                    <span class="badge bg-info-subtle text-info border font-semibold px-2 py-1">
                                        <i class="fa-solid fa-folder me-1"></i>{{ $cat->parent->name }}
                                    </span>
                                @else
                                    <span class="badge bg-primary-subtle text-primary border font-semibold px-2 py-1">Root Category</span>
                                @endif
                            </td>
                            <td><code>{{ $cat->slug }}</code></td>
                            <td><span class="badge bg-light text-dark border">{{ $cat->sort_order }}</span></td>
                            <td>
                                @if($cat->status)
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">Disabled</span>
                                @endif
                            </td>
                            <td><span class="badge bg-secondary-subtle text-secondary px-3 py-1 font-semibold">{{ $cat->getAllProductsRecursive()->count() }} Products</span></td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('admin.categories.create', ['parent_id' => $cat->id]) }}" class="btn btn-sm btn-light border text-success" title="Add Child Category">
                                        <i class="fa-solid fa-plus"></i> Child
                                    </a>
                                    <a href="{{ route('admin.categories.edit', $cat->id) }}" class="btn btn-sm btn-light border text-primary" title="Edit Category">
                                        <i class="fa-solid fa-pen"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border text-danger" title="Delete Category">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">No categories recorded.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

