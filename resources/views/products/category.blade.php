@extends('layouts.app')

@section('title', ($category->name ?? 'Category Products') . ' — SR Chemical Industries Limited')

@section('content')
    <style>
    body { background-image: url("{{ asset('assets/img/added/bg-1.svg') }}") !important; background-size: 350px !important; background-position: right top !important; background-repeat: no-repeat !important; background-color: #f4f8fd !important; }
    </style>

    <!--=====HERO AREA START=======-->
    <div class="common-hero" style="background-image: url('{{ asset('assets/img/bg/research-hero-bg.jpg') }}'); background-size: cover; background-position: center;">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="common-hero-heading">
                        <h1 class="text-60 sm:text-30 md:text-30 leading-56 font-semibold white">{{ $category->name }}</h1>
                        <div class="page-change">
                            <ul>
                                <li class="inline-block"><a href="{{ route('home') }}" class="inline-block text-16 leading-16 white font-semibold">Home</a></li>
                                <li class="inline-block arrow text-16 leading-16 white font-normal"><i class="fa-solid fa-angle-right"></i></li>
                                <li class="inline-block text-16 leading-16 white font-normal">{{ $category->name }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--=====HERO AREA END=======-->

    <!--=====PRODUCT LISTING AREA START=======-->
    <div class="service sp" style="background: var(--light-gray);">
        <div class="container">
            <div class="row">
                @forelse($products as $product)
                <div class="col-lg-4 col-md-6 mb-30">
                    <div class="research-box sm:mt-30">
                        <div class="image image-anime _relative">
                            <img loading="lazy" class="w-full" src="{{ asset($product->image_url) }}" alt="{{ $product->name }}">
                        </div>
                        <div class="heading1">
                            <h4><a href="{{ route('products.show', $product->slug) }}" class="text-20 leading-20 font-semibold title1">{{ $product->name }}</a></h4>
                            <p class="mb-20 mt-16 text-16 font-normal pera1 leading-26">{{ Str::limit($product->description, 100) }}</p>
                            <a href="{{ route('products.show', $product->slug) }}" class="learn text-16 leading-16 font-semibold title1">Read More 
                                <span class="arrow1"><i class="fa-solid fa-arrow-right"></i></span>
                                <span class="arrow2"><i class="fa-solid fa-arrow-right"></i></span>
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 py-5 text-center">
                    <h4 class="text-20 font-semibold text-dark">No products found in this category.</h4>
                </div>
                @endforelse
            </div>

            @if(method_exists($products, 'links'))
            <div class="mt-4 d-flex justify-content-center">
                {{ $products->links() }}
            </div>
            @endif
        </div>
    </div>
    <!--=====PRODUCT LISTING AREA END=======-->

    <!--=====CTA AREA START=======-->
    <div class="cta1 sp" style="background: linear-gradient(rgba(13, 39, 68, 0.85), rgba(0, 0, 0, 0.6)), url('{{ asset('assets/img/added/cta-banner.jpg') }}'); background-position: center; background-size: cover; background-repeat: no-repeat;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-5">
                    <h2 class="text-44 sm:text-30 md:text-30 leading-56 font-semibold white">Ready to Partner for Quality Chemical Solutions?</h2>
                </div>
                <div class="col-lg-7">
                    <div class="buttons text-end md:text-start xs:text-start sm:mt-20 md:mt-20">
                        <a class="theme-btn1" href="{{ route('contact') }}">Contact Us Now 
                            <span class="arrow1"><i class="fa-solid fa-arrow-right"></i></span>
                            <span class="arrow2"><i class="fa-solid fa-arrow-right"></i></span>
                        </a>
                        <a class="theme-btn2 ml-16 sm:ml-0 sm:mt-20" href="{{ route('contact') }}">Get Inquiry Now 
                            <span class="arrow1"><i class="fa-solid fa-arrow-right"></i></span>
                            <span class="arrow2"><i class="fa-solid fa-arrow-right"></i></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--=====CTA AREA END=======-->
@endsection
