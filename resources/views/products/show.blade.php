@extends('layouts.app')

@section('title', $product->name . ' | SR Chemical Industries Limited')

@section('content')
    <style>
    body { background-image: url("{{ asset('assets/img/added/bg-1.svg') }}") !important; background-size: 350px !important; background-position: right top !important; background-repeat: no-repeat !important; background-color: #f4f8fd !important; }
    </style>

    <!-- 1. Hero Banner Title & 2. Breadcrumb Text -->
    <div class="common-hero" style="background-image: url('{{ asset('assets/img/added/COLOR.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="common-hero-heading">
                        <h1 class="text-60 sm:text-30 md:text-30 leading-56 font-semibold white">Product Details</h1>
                        <div class="page-change">
                            <ul>
                                <li class="inline-block"><a href="{{ route('home') }}" class="inline-block text-16 leading-16 white font-semibold">Home</a></li>
                                <li class="inline-block arrow text-16 leading-16 white font-normal"><i class="fa-solid fa-angle-right"></i></li>
                                <li class="inline-block text-16 leading-16 white font-normal">{{ $product->name }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="sp" style="background: var(--light-gray);">
        <div class="container">
            <div class="row align-items-center">

                <div class="col-md-6">
                    <div class="product-img-box" style="background:#fff; border-radius:14px; padding:24px; box-shadow:0 6px 30px rgba(15,82,134,0.12); border:1.5px solid rgba(15,82,134,0.10); position:sticky; top:100px;">
                        @if(!empty($product->image_url))
                            <img loading="lazy" src="{{ asset($product->image_url) }}" alt="{{ $product->name }}" style="width:100%; border-radius:10px;">
                        @else
                            <div class="text-center py-5 bg-light rounded" style="min-height: 250px; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                                <i class="fa-solid fa-image text-secondary mb-3 display-4"></i>
                                <span class="text-muted font-bold text-15">Image Not Available</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-md-6 sm:mt-30">
                    <div class="details-content" style="background:#fff; border-radius:14px; padding:32px 36px; box-shadow:0 6px 30px rgba(15,82,134,0.08); border:1px solid #e2e8f0;">
                        <!-- 1. Product Title (Image 1 match) -->
                        <h2 style="font-family:'Barlow',sans-serif; font-weight:800; font-size:26px; color:#0b3b60; line-height:1.2; margin-bottom:4px;">{{ $product->name }}</h2>
                        
                        <!-- 2. Chemical Sub-Name (Image 1 match) -->
                        <p style="font-size:14px; font-weight:500; color:#2563eb; margin-bottom:24px;">{{ $product->chemical_name ?? $product->name }}</p>

                        <!-- 3. Key Attributes: Brand, HSN Code, Packaging -->
                        <p style="font-size:15px !important; margin-bottom:10px !important; color:#1e293b !important;">
                            <b style="font-weight:700 !important; color:#0f172a !important;">Brand :</b> {{ $product->brand ?? 'GNFC' }}
                        </p>
                        <p style="font-size:15px !important; margin-bottom:10px !important; color:#1e293b !important;">
                            <b style="font-weight:700 !important; color:#0f172a !important;">HSN Code :</b> {{ $product->hsn_code ?? 'N/A' }}
                        </p>
                        <p style="font-size:15px !important; margin-bottom:22px !important; color:#1e293b !important;">
                            <b style="font-weight:700 !important; color:#0f172a !important;">Packaging :</b> {{ $product->packaging ?? 'Standard Packaging' }}
                        </p>

                        <!-- 4. DESCRIPTION : Section (Normal standard readable sizing) -->
                        @if($product->description)
                        <div style="margin-bottom:20px !important;">
                            <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px;">
                                <span style="display:inline-block; width:3.5px; height:16px; background-color:#22c55e; border-radius:2px;"></span>
                                <span style="font-size:15px !important; font-weight:700 !important; color:#0b3b60 !important; letter-spacing:0.5px; text-transform:uppercase;">DESCRIPTION :</span>
                            </div>
                            <p style="font-size:14px !important; font-weight:400 !important; color:#334155 !important; line-height:22px !important; margin:0 !important;">
                                {{ $product->description }}
                            </p>
                        </div>
                        @endif

                        <!-- 5. APPLICATION : Section (Normal standard readable sizing) -->
                        @if(!empty($product->applications))
                        <div style="margin-bottom:20px !important;">
                            <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px;">
                                <span style="display:inline-block; width:3.5px; height:16px; background-color:#22c55e; border-radius:2px;"></span>
                                <span style="font-size:15px !important; font-weight:700 !important; color:#0b3b60 !important; letter-spacing:0.5px; text-transform:uppercase;">APPLICATION :</span>
                            </div>
                            <p style="font-size:14px !important; font-weight:400 !important; color:#334155 !important; line-height:22px !important; margin:0 !important;">
                                @if(is_array($product->applications))
                                    {{ implode(', ', $product->applications) }}
                                @else
                                    {{ $product->applications }}
                                @endif
                            </p>
                        </div>
                        @endif

                        <!-- 6. Action Buttons: MSDS, Specification & Back -->
                        <div style="display:flex; flex-wrap:wrap; align-items:center; gap:12px; margin-top:28px;">
                            @if(!empty($product->msds_pdf_url) && file_exists(public_path(ltrim($product->msds_pdf_url, '/'))))
                            <a href="{{ asset($product->msds_pdf_url) }}" target="_blank" style="background:#059669; color:#fff; font-size:14px; font-weight:600; padding:10px 24px; border-radius:3px; text-decoration:none; display:inline-block; transition:all 0.2s ease;">
                                MSDS
                            </a>
                            @elseif(!empty($product->msds_url) && file_exists(public_path(ltrim(str_replace('\\', '/', $product->msds_url), '/'))))
                            <a href="{{ asset(ltrim(str_replace('\\', '/', $product->msds_url), '/')) }}" target="_blank" style="background:#059669; color:#fff; font-size:14px; font-weight:600; padding:10px 24px; border-radius:3px; text-decoration:none; display:inline-block; transition:all 0.2s ease;">
                                MSDS
                            </a>
                            @endif

                            @if(!empty($product->spec_pdf_url) && file_exists(public_path(ltrim($product->spec_pdf_url, '/'))))
                            <a href="{{ asset($product->spec_pdf_url) }}" target="_blank" style="background:#2563eb; color:#fff; font-size:14px; font-weight:600; padding:10px 24px; border-radius:3px; text-decoration:none; display:inline-block; transition:all 0.2s ease;">
                                Specification
                            </a>
                            @elseif(!empty($product->specification_url) && file_exists(public_path(ltrim(str_replace('\\', '/', $product->specification_url), '/'))))
                            <a href="{{ asset(ltrim(str_replace('\\', '/', $product->specification_url), '/')) }}" target="_blank" style="background:#2563eb; color:#fff; font-size:14px; font-weight:600; padding:10px 24px; border-radius:3px; text-decoration:none; display:inline-block; transition:all 0.2s ease;">
                                Specification
                            </a>
                            @endif

                            <a href="javascript:history.back()" style="background:#f1f5f9; color:#0b3b60; font-size:14px; font-weight:600; padding:10px 20px; border-radius:3px; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                                <i class="fa-solid fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
