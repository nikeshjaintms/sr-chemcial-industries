@extends('layouts.app')

@section('title', 'Industry Technical Specifications Guide — SR Chemical Industries Limited')

@section('content')
<div class="py-5 bg-light border-bottom">
    <div class="container">
        <h1 class="text-36 font-semibold title1 text-dark mb-2">Technical Specifications Guide</h1>
        <p class="text-muted text-16 mb-0">Chemical grades, purity benchmarks, and industrial handling compliance</p>
    </div>
</div>

<div class="py-5 bg-white">
    <div class="container">
        <div class="p-4 border rounded bg-light">
            <h3 class="text-24 font-semibold text-dark mb-3">SR Chemicals Industrial Reference</h3>
            <p class="text-16 text-muted leading-26">
                Welcome to SR Chemical Industries Limited's technical documentation resource. We provide detailed specifications, CAS references, HSN classifications, and safety handling procedures for all 60+ chemical raw materials supplied across domestic and global trade networks.
            </p>
            <div class="mt-4">
                <a href="{{ route('products.index') }}" class="theme-btn1">Explore Products Catalog
                    <span class="arrow1"><i class="fa-solid fa-arrow-right"></i></span>
                    <span class="arrow2"><i class="fa-solid fa-arrow-right"></i></span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
