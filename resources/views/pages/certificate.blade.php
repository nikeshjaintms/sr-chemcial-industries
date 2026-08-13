@extends('layouts.app')

@section('title', 'Quality Certificates & Compliance | SRCIL')

@section('content')
    <style>
    body { background-image: url("{{ asset('assets/img/added/bg-1.svg') }}") !important; background-size: 350px !important; background-position: right top !important; background-repeat: no-repeat !important; background-color: #f4f8fd !important; }
    .certificate-pdf-card {
        background: #fff;
        padding: 30px;
        border: 2px solid #eef2f7;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        cursor: pointer;
        display: block;
        text-decoration: none;
    }
    .certificate-pdf-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        border-color: #67B346 !important;
    }
    .certificate-pdf-card .pdf-icon {
        font-size: 60px;
        color: #e74c3c;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }
    .certificate-pdf-card:hover .pdf-icon {
        transform: scale(1.1);
    }
    .cert-lightbox {
        display: none;
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.85);
        backdrop-filter: blur(5px);
        z-index: 99999;
        justify-content: center;
        align-items: center;
        padding: 30px 15px;
    }
    .cert-lightbox-inner {
        position: relative;
        max-width: 850px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        padding: 12px;
        margin: auto;
        scrollbar-width: thin;
        scrollbar-color: #0F5286 #F1F5F9;
    }
    .cert-lightbox-inner::-webkit-scrollbar {
        width: 8px;
    }
    .cert-lightbox-inner::-webkit-scrollbar-track {
        background: #F1F5F9;
        border-radius: 10px;
    }
    .cert-lightbox-inner::-webkit-scrollbar-thumb {
        background: #0F5286;
        border-radius: 10px;
    }
    .cert-lightbox-inner img {
        width: 100%;
        height: auto;
        display: block;
        border-radius: 10px;
    }
    .cert-close {
        position: fixed;
        top: 25px;
        right: 35px;
        background: rgba(255, 255, 255, 0.2);
        border: 2px solid rgba(255, 255, 255, 0.4);
        color: #fff;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        font-size: 22px;
        cursor: pointer;
        z-index: 100000;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    .cert-close:hover {
        background: #e74c3c;
        border-color: #e74c3c;
        color: #fff;
        transform: scale(1.1);
    }
    .cert-card {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        cursor: pointer;
        transition: transform 0.3s ease;
    }
    .cert-card:hover {
        transform: translateY(-5px);
    }
    .cert-img-wrap img {
        width: 100%;
        height: auto;
        display: block;
    }
    </style>

    <!--=====HERO AREA START=======-->

    <div class="common-hero" style="background-image: url('{{ asset('assets/img/added/COLOR.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
        <div class="container">
            <div class="row">
                <div class="col-lg-10">
                    <div class="common-hero-heading">
                        <h1 class="text-60 sm:text-30 md:text-30 leading-56 font-semibold white">Certificate</h1>
                        <div class="page-change">
                            <ul>
                                <li class="inline-block"><a href="{{ route('home') }}"
                                        class="inline-block text-16 leading-16 white font-semibold">Home</a></li>
                                <li class="inline-block arrow text-16 leading-16 white font-normal"><i
                                        class="fa-solid fa-angle-right"></i></li>
                                <li class="inline-block text-16 leading-16 white font-normal">Certificate</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--=====HERO AREA END=======-->

    <!--=====CONTACT AREA START=======-->

    <div class="cert-section sp">
        <div class="container">

            <div class="row justify-content-center g-4">

                <div class="col-lg-4 col-md-6 col-sm-10">
                    <div class="cert-card" onclick="openCert('{{ asset('assets/img/added/Certificate/certificate 1.png') }}', 'Certificate 1')">
                        <div class="cert-img-wrap">
                            <img src="{{ asset('assets/img/added/Certificate/certificate 1.png') }}" alt="Certificate 1" loading="lazy">
                        </div>
                    </div>
                </div>


                <div class="col-lg-4 col-md-6 col-sm-10">
                    <div class="cert-card" onclick="openCert('{{ asset('assets/img/added/Certificate/2.jpg') }}', 'ISO Certificate')">
                        <div class="cert-img-wrap">
                            <img src="{{ asset('assets/img/added/Certificate/2.jpg') }}" alt="ISO Certificate" loading="lazy">
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 col-sm-10">
                    <div class="cert-card" onclick="openCert('{{ asset('assets/img/added/Certificate/1.jpg') }}', 'GST Certificate')">
                        <div class="cert-img-wrap">
                            <img src="{{ asset('assets/img/added/Certificate/1.jpg') }}" alt="GST Certificate" loading="lazy">
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 col-sm-10">
                    <div class="cert-card" onclick="openCert('{{ asset('assets/img/added/Certificate/ISHP.jpg') }}', 'ISHP Certificate')">
                        <div class="cert-img-wrap">
                            <img src="{{ asset('assets/img/added/Certificate/ISHP.jpg') }}" alt="ISHP Certificate" loading="lazy">
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- Lightbox -->
    <div class="cert-lightbox" id="certLightbox" onclick="closeCert()">
        <div class="cert-lightbox-inner" onclick="event.stopPropagation()">
            <button class="cert-close" onclick="closeCert()"><i class="fa-solid fa-xmark"></i></button>
            <img id="certLightboxImg" src="" alt="">
        </div>
    </div>

    <!--=====CONTACT AREA END=======-->

    <!--=====CTA AREA START=======-->

    <div class="cta1 sp" style="background: linear-gradient(rgba(13, 39, 68, 0.85), rgba(0, 0, 0, 0.6)), url('{{ asset('assets/img/added/cta-banner.jpg') }}'); background-position: center; background-size: cover; background-repeat: no-repeat;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-5">
                    <h2 class="text-44 sm:text-30 md:text-30 leading-56 font-semibold white">Ready to Innovate in Chemical &amp; Metrical?</h2>
                </div>
                <div class="col-lg-7">
                    <div class="buttons text-end md:text-start xs:text-start sm:mt-20 md:mt-20">
                        <a class="theme-btn1" href="{{ route('contact') }}">Contact Us Now
                            <span class="arrow1"><i class="fa-solid fa-arrow-right"></i></span>
                            <span class="arrow2"><i class="fa-solid fa-arrow-right"></i></span>
                        </a>
                        <a class="theme-btn2 ml-16 sm:ml-0 sm:mt-20" href="{{ route('home') }}">Get Inquiry Now
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

@push('scripts')
<script>
function openCert(src, title) {
    document.getElementById('certLightboxImg').src = src;
    document.getElementById('certLightbox').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeCert() {
    document.getElementById('certLightbox').style.display = 'none';
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeCert();
});
</script>
@endpush
