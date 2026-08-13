@extends('admin.layouts.admin')

@section('title', 'Replace Duplicate Product Images — SRCIL Admin ERP')
@section('page_title', 'Duplicate / Placeholder Image Management')

@section('content')
<div class="card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary font-bold"><i class="fa-solid fa-images me-2"></i> Find & Replace Duplicate Product Images</h4>
            <p class="text-muted mb-0">Identify products that share duplicate placeholder images and replace them with their distinct correct chemical product image.</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Product Catalog
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(empty($audit['duplicate_images']))
    <div class="alert alert-success py-4 text-center">
        <i class="fa-solid fa-circle-check display-4 mb-2 text-success"></i>
        <h5>No Duplicate Image Assignments Found!</h5>
        <p class="mb-0">All active products currently have unique image assignments.</p>
    </div>
    @else

    @foreach($audit['duplicate_images'] as $dup)
    <div class="card border mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
            <div class="d-flex align-items-center gap-3">
                <img src="{{ asset($dup['image_url']) }}" style="height:45px; width:45px; object-fit:cover;" class="rounded border">
                <div>
                    <h6 class="font-bold mb-0 text-dark">{{ basename($dup['image_url']) }}</h6>
                    <span class="badge bg-warning text-dark text-11">Assigned to {{ $dup['count'] }} products</span>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width:60px;">ID</th>
                            <th>Product Name</th>
                            <th>Current Image Path</th>
                            <th style="width:300px;">Replace With Image</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dup['products'] as $pItem)
                        @php
                            $fullProd = $products->firstWhere('id', $pItem['id']);
                        @endphp
                        <tr>
                            <td>{{ $pItem['id'] }}</td>
                            <td class="font-bold">{{ $pItem['name'] }}</td>
                            <td class="text-muted text-12">{{ $fullProd ? $fullProd->image_url : 'N/A' }}</td>
                            <td>
                                <form action="{{ route('admin.products.replace-duplicate-image') }}" method="POST" class="d-flex gap-2">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $pItem['id'] }}">
                                    <select name="image_url" class="form-select form-select-sm" required>
                                        <option value="">-- Choose Image --</option>
                                        @foreach($candidateImages as $cImg)
                                        <option value="{{ $cImg['relative_path'] }}">{{ $cImg['filename'] }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-brand-primary text-nowrap">Replace</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endforeach

    @endif
</div>
@endsection
