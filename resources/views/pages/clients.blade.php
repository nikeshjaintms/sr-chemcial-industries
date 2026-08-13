@extends('layouts.app')

@section('title', 'Our Clients | Trusted Chemical Partner in India | SRCIL')

@section('content')
    <style>
    body { background-image: url("{{ asset('assets/img/added/bg-1.svg') }}") !important; background-size: 350px !important; background-position: right top !important; background-repeat: no-repeat !important; background-color: #f4f8fd !important; }
    </style>

    <!--=====HERO AREA START=======-->

    <div class="common-hero" style="background-image: url('{{ asset('assets/img/added/COLOR.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
        <div class="container">
            <div class="row">
                <div class="col-lg-10">
                    <div class="common-hero-heading">
                        <h1 class="text-60 sm:text-30 md:text-30 leading-56 font-semibold white">Clients</h1>
                        <div class="page-change">
                            <ul>
                                <li class="inline-block"><a href="{{ route('home') }}" class="inline-block text-16 leading-16 white font-semibold">Home</a></li>
                                <li class="inline-block arrow text-16 leading-16 white font-normal"><i class="fa-solid fa-angle-right"></i></li>
                                <li class="inline-block text-16 leading-16 white font-normal">Clients</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--=====HERO AREA END=======-->

    <!-- ===== COMPANY WE REPRESENT ===== -->
    <div class="sp" style="background: var(--light-gray); border-top: 4px solid var(--brand-green);">
        <div class="container">
            <h2 class="we-represent-title">Company We Represent</h2>
            <div class="represent-grid mt-3">
                @php
                $represent1 = [
                  ['gacl.png', 'GACL'],
                  ['epigral.png', 'Epigral'],
                  ['dcm_shriram.png', 'DCM Shriram'],
                  ['dmcc.png', 'SRF'],
                  ['chemie.png', 'Chemie'],
                ];
                @endphp
                @foreach ($represent1 as $r)
                @php
                  $imgPath1 = file_exists(public_path('assets/img/added/clients/' . $r[0]))
                    ? 'assets/img/added/clients/' . $r[0]
                    : 'assets/img/added/' . $r[0];
                @endphp
                <div class="srcil-represent-card">
                    <img loading="lazy" src="{{ asset($imgPath1) }}" alt="{{ $r[1] }}">
                </div>
                @endforeach
            </div>

            <!-- OUR CLIENTS Section -->
            <div style="margin-top: 60px;">
                <h2 class="we-represent-title" style="margin-bottom: 8px;">Our Clients</h2>
                <div class="represent-grid">
                    @php
                    $represent2 = [
                      ['karan.png', 'Karan Industries'],
                      ['si.png', 'SI'],
                      ['aero.png', 'Aero Agro Chemical Industries Ltd'],
                      ['Loki Industries.png', 'Loki Industries'],
                      ['Sujal Enterprises.jpg', 'Sujal Enterprises, Bharuch'],
                      ['asha_resins.png', 'Asha Mineral'],
                      ['Element Chemlink.jpg', 'Element Chemlink'],
                      ['Transrail-Final-Logo.png', 'Transrail'],
                      ['images.png', 'Client'],
                    ];
                    @endphp
                    @foreach ($represent2 as $r)
                    @php
                      $imgPath2 = file_exists(public_path('assets/img/added/clients/' . $r[0]))
                        ? 'assets/img/added/clients/' . $r[0]
                        : 'assets/img/added/' . $r[0];
                    @endphp
                    <div class="srcil-represent-card">
                        <img loading="lazy" src="{{ asset($imgPath2) }}" alt="{{ $r[1] }}">
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- ===== INDUSTRIES WE SERVE ===== -->
    <section class="industries-section">
      <div class="container">
        <h2 class="clients-brochure-title">Industries We Serve</h2>

        <div class="industries-grid">

          <div class="industry-card">
            <div class="iws-img-wrap">
              <img loading="lazy" src="{{ asset('assets/img/added/iws/agriculture.jpg') }}" alt="Agriculture Industry">
            </div>
            <h3>Agriculture Industry</h3>
          </div>

          <div class="industry-card">
            <div class="iws-img-wrap">
              <img loading="lazy" src="{{ asset('assets/img/added/iws/textile.jpg') }}" alt="Textile Industry">
            </div>
            <h3>Textile Industry</h3>
          </div>

          <div class="industry-card">
            <div class="iws-img-wrap">
              <img loading="lazy" src="{{ asset('assets/img/added/iws/dairy.jpg') }}" alt="Dairy Industry">
            </div>
            <h3>Dairy Industry</h3>
          </div>

          <div class="industry-card">
            <div class="iws-img-wrap">
              <img loading="lazy" src="{{ asset('assets/img/added/iws/paper.jpg') }}" alt="Paper Industry">
            </div>
            <h3>Paper Industry</h3>
          </div>

          <div class="industry-card">
            <div class="iws-img-wrap">
              <img loading="lazy" src="{{ asset('assets/img/added/iws/fertilizer.jpg') }}" alt="Fertilizer Industry">
            </div>
            <h3>Fertilizer Industry</h3>
          </div>

          <div class="industry-card">
            <div class="iws-img-wrap">
              <img loading="lazy" src="{{ asset('assets/img/added/iws/dyes.jpg') }}" alt="Dyes & Intermediates">
            </div>
            <h3>Dyes &amp; Intermediates</h3>
          </div>

          <div class="industry-card">
            <div class="iws-img-wrap">
              <img loading="lazy" src="{{ asset('assets/img/added/iws/pharma.jpg') }}" alt="Pharmaceutical Industry">
            </div>
            <h3>Pharmaceutical Industry</h3>
          </div>

          <div class="industry-card">
            <div class="iws-img-wrap">
              <img loading="lazy" src="{{ asset('assets/img/added/iws/sugar.jpg') }}" alt="Sugar Industry">
            </div>
            <h3>Sugar Industry</h3>
          </div>

          <div class="industry-card">
            <div class="iws-img-wrap">
              <img loading="lazy" src="{{ asset('assets/img/added/iws/soap.jpg') }}" alt="Soap & Detergent">
            </div>
            <h3>Soap &amp; Detergent</h3>
          </div>

          <div class="industry-card">
            <div class="iws-img-wrap">
              <img loading="lazy" src="{{ asset('assets/img/added/iws/rubber.jpg') }}" alt="Rubber & Tyre Industry">
            </div>
            <h3>Rubber &amp; Tyre Industry</h3>
          </div>

        </div>
      </div>
    </section>

    <!-- ===== CTA ===== -->
    <div class="cta1 sp" style="background: linear-gradient(rgba(13,39,68,0.88), rgba(0,0,0,0.65)), url('{{ asset('assets/img/added/cta-banner.jpg') }}'); background-position: center; background-size: cover; background-repeat: no-repeat;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-5">
                    <h2 class="text-44 sm:text-30 md:text-30 leading-56 font-semibold white">Ready to Partner for Quality Chemical Solutions?</h2>
                </div>
                <div class="col-lg-7">
                    <div class="buttons text-end md:text-start xs:text-start sm:mt-20 md:mt-20">
                        <a class="theme-btn1" href="{{ route('contact') }}">Contact Us Now <span class="arrow1"><i class="fa-solid fa-arrow-right"></i></span><span class="arrow2"><i class="fa-solid fa-arrow-right"></i></span></a>
                        <a class="theme-btn2 ml-16 sm:ml-0 sm:mt-20" href="{{ route('contact') }}">Get Inquiry Now <span class="arrow1"><i class="fa-solid fa-arrow-right"></i></span><span class="arrow2"><i class="fa-solid fa-arrow-right"></i></span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
