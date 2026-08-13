@extends('layouts.app')

@section('title', 'Technical Blogs & Articles — SR Chemical Industries Limited')

@section('content')
<div class="py-5 bg-light border-bottom">
    <div class="container">
        <h1 class="text-36 font-semibold title1 text-dark mb-2">Technical Blogs & Chemical Guides</h1>
        <p class="text-muted text-16 mb-0">Insights on Chlor-Alkali manufacturing, Water Treatment, and Solvent Sourcing</p>
    </div>
</div>

<div class="py-5 bg-white">
    <div class="container">
        <div class="row g-4">
            @foreach($blogs as $blog)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden">
                    <img src="{{ asset($blog->image_url ?? 'assets/img/added/product/Caustic-Soda-Flakes-NaOH.jpg') }}" class="card-img-top" alt="{{ $blog->title }}" style="height: 200px; object-fit: cover;">
                    <div class="card-body p-4 d-flex flex-column">
                        <span class="badge bg-primary-subtle text-primary w-auto align-self-start mb-2">{{ $blog->category ?? 'Technical Guide' }}</span>
                        <h5 class="card-title text-18 font-semibold text-dark mb-2">{{ $blog->title }}</h5>
                        <p class="card-text text-muted text-14 mb-3 flex-grow-1">{{ Str::limit($blog->summary, 100) }}</p>
                        <div class="pt-3 border-top d-flex justify-content-between align-items-center">
                            <span class="text-12 text-muted"><i class="fa-solid fa-clock me-1"></i>{{ $blog->read_time }}</span>
                            <a href="{{ route('blog.show', $blog->slug) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Read Article</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-5 d-flex justify-content-center">
            {{ $blogs->links() }}
        </div>
    </div>
</div>
@endsection
