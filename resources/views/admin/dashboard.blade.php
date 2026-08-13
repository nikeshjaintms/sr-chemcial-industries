@extends('admin.layouts.admin')

@section('title', 'Dashboard Overview — SRCIL Admin ERP')
@section('page_title', 'Dashboard Overview')

@section('content')
<!-- Dashboard Stat Cards Row -->
<div class="row g-4 mb-4">
    <!-- Total Products -->
    <div class="col-xl-3 col-md-6">
        <div class="stat-card-gradient stat-card-blue">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label">Total Products</div>
                    <div class="stat-value">{{ $totalProducts }}</div>
                </div>
                <div class="stat-icon-wrap">
                    <i class="fa-solid fa-flask"></i>
                </div>
            </div>
            <div class="mt-3 text-12 opacity-75">
                <i class="fa-solid fa-square-check me-1"></i> {{ $featuredProductsCount }} Featured Items
            </div>
        </div>
    </div>

    <!-- Total Categories -->
    <div class="col-xl-3 col-md-6">
        <div class="stat-card-gradient stat-card-green">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label">Product Categories</div>
                    <div class="stat-value">{{ $totalCategories }}</div>
                </div>
                <div class="stat-icon-wrap">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
            </div>
            <div class="mt-3 text-12 opacity-75">
                <i class="fa-solid fa-layer-group me-1"></i> Active Chemical Groups
            </div>
        </div>
    </div>

    <!-- Total Blogs -->
    <div class="col-xl-3 col-md-6">
        <div class="stat-card-gradient stat-card-purple">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label">Technical Articles</div>
                    <div class="stat-value">{{ $totalBlogs }}</div>
                </div>
                <div class="stat-icon-wrap">
                    <i class="fa-solid fa-newspaper"></i>
                </div>
            </div>
            <div class="mt-3 text-12 opacity-75">
                <i class="fa-solid fa-book-open me-1"></i> Industry Knowledge Base
            </div>
        </div>
    </div>

    <!-- Chatbot Queries -->
    <div class="col-xl-3 col-md-6">
        <div class="stat-card-gradient stat-card-cyan">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label">AI Chat Conversations</div>
                    <div class="stat-value">{{ $totalChatQueries }}</div>
                </div>
                <div class="stat-icon-wrap">
                    <i class="fa-solid fa-comments"></i>
                </div>
            </div>
            <div class="mt-3 text-12 opacity-75">
                <i class="fa-solid fa-bolt me-1"></i> Customer AI Enquiries
            </div>
        </div>
    </div>
</div>

<!-- Main Section: Recent Products & Quick Actions -->
<div class="row g-4">
    <!-- Recent Products Table -->
    <div class="col-lg-8">
        <div class="card-custom p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h5 font-bold text-dark mb-1">Recently Added Products</h2>
                    <p class="text-13 text-muted mb-0">Latest chemical products added to the catalog</p>
                </div>
                <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 font-semibold">
                    View All Products <i class="fa-solid fa-arrow-right ms-1"></i>
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-custom align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>CAS Number</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentProducts as $p)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ asset($p->image_url) }}" alt="{{ $p->name }}" width="42" height="42" class="rounded-3 object-fit-cover border">
                                    <div>
                                        <div class="font-bold text-dark text-14">{{ $p->name }}</div>
                                        <div class="text-12 text-muted">{{ $p->brand ?? 'SRCIL' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-primary border px-2 py-1 text-12">
                                    {{ $p->category ? $p->category->name : 'General' }}
                                </span>
                            </td>
                            <td><code class="text-dark">{{ $p->cas_number ?? 'N/A' }}</code></td>
                            <td>
                                @if($p->is_featured)
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1"><i class="fa-solid fa-star me-1"></i>Featured</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">Standard</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.products.edit', $p->id) }}" class="btn btn-sm btn-light border text-primary">
                                    <i class="fa-solid fa-pen"></i> Edit
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No products recorded.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Operations -->
    <div class="col-lg-4">
        <div class="card-custom p-4 h-100 d-flex flex-column">
            <h2 class="h5 font-bold text-dark mb-1">Quick Actions</h2>
            <p class="text-13 text-muted mb-4">Frequently used admin operations</p>

            <div class="d-grid gap-3 flex-grow-1">
                <a href="{{ route('admin.products.create') }}" class="p-3 border rounded-3 text-decoration-none d-flex align-items-center gap-3 bg-light hover-bg-white transition">
                    <div class="bg-primary text-white rounded-3 p-3">
                        <i class="fa-solid fa-plus text-20"></i>
                    </div>
                    <div>
                        <div class="font-bold text-dark text-15">Add New Product</div>
                        <div class="text-12 text-muted">Upload product specs, images, and MSDS</div>
                    </div>
                </a>

                <a href="{{ route('admin.categories.create') }}" class="p-3 border rounded-3 text-decoration-none d-flex align-items-center gap-3 bg-light hover-bg-white transition">
                    <div class="bg-success text-white rounded-3 p-3">
                        <i class="fa-solid fa-layer-group text-20"></i>
                    </div>
                    <div>
                        <div class="font-bold text-dark text-15">Create Category</div>
                        <div class="text-12 text-muted">Add new industrial chemical category</div>
                    </div>
                </a>

                <a href="{{ route('admin.products.index') }}" class="p-3 border rounded-3 text-decoration-none d-flex align-items-center gap-3 bg-light hover-bg-white transition">
                    <div class="bg-info text-white rounded-3 p-3">
                        <i class="fa-solid fa-list-check text-20"></i>
                    </div>
                    <div>
                        <div class="font-bold text-dark text-15">Manage Products</div>
                        <div class="text-12 text-muted">View, edit, filter, or delete products</div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
