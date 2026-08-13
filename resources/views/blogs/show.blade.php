@extends('layouts.app')

@section('title', $blog->title . ' — SR Chemical Industries Limited')

@section('content')
<div class="py-4 bg-light border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('blog.index') }}">Blog</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($blog->title, 40) }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="py-5 bg-white">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8">
                <span class="badge bg-primary px-3 py-2 text-14 mb-3">{{ $blog->category ?? 'Technical Guide' }}</span>
                <h1 class="text-36 font-semibold text-dark mb-3">{{ $blog->title }}</h1>
                <div class="d-flex align-items-center text-muted text-14 mb-4 pb-3 border-bottom">
                    <span class="me-4"><i class="fa-solid fa-user me-1"></i>{{ $blog->author ?? 'SRCIL Editorial Team' }}</span>
                    <span class="me-4"><i class="fa-solid fa-calendar me-1"></i>{{ $blog->published_at ?? '2026' }}</span>
                    <span><i class="fa-solid fa-clock me-1"></i>{{ $blog->read_time }}</span>
                </div>

                <div class="mb-4 text-center">
                    <img src="{{ asset($blog->image_url ?? 'assets/img/added/product/Caustic-Soda-Flakes-NaOH.jpg') }}" alt="{{ $blog->title }}" class="img-fluid rounded shadow-sm max-h-400">
                </div>

                <div class="blog-content text-16 leading-28 text-muted">
                    {!! nl2br(e($blog->content)) !!}
                </div>
            </div>

            <div class="col-lg-4">
                <div class="p-4 bg-light border rounded mb-4">
                    <h4 class="text-20 font-semibold text-dark mb-3">Recent Technical Articles</h4>
                    <div class="list-group list-group-flush">
                        @foreach($recentBlogs as $recent)
                        <a href="{{ route('blog.show', $recent->slug) }}" class="list-group-item list-group-item-action bg-transparent border-0 px-0 py-2">
                            <h6 class="text-15 font-semibold text-dark mb-1">{{ $recent->title }}</h6>
                            <small class="text-muted">{{ $recent->published_at }}</small>
                        </a>
                        @endforeach
                    </div>
                </div>

                <div class="p-4 bg-primary text-white rounded text-center">
                    <h4 class="text-20 font-semibold text-white mb-2">Need Direct Chemical Quote?</h4>
                    <p class="text-14 text-white-50 mb-3">Get technical pricing for bulk orders or ISO tanker dispatches.</p>
                    <a href="{{ route('contact') }}" class="btn btn-light text-primary font-bold w-100">Submit Inquiry</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
