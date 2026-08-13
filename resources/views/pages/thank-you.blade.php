@extends('layouts.app')

@section('title', 'SR Chemical Industries Limited | Thank You')

@section('content')
    <style>
    body { background-image: url("{{ asset('assets/img/added/bg-1.svg') }}") !important; background-size: 350px !important; background-position: right top !important; background-repeat: no-repeat !important; background-color: #f4f8fd !important; }
    </style>

    <div class="common-hero" style="background: url('{{ asset('assets/img/bg/research-hero-bg.jpg') }}') no-repeat center center; background-size: cover;">
        <div class="container">
            <div class="row">
                <div class="col-lg-10">
                    <div class="common-hero-heading">
                        <h1 class="text-60 sm:text-30 md:text-30 leading-56 font-semibold white">Thank You</h1>
                        <div class="page-change">
                            <ul>
                                <li class="inline-block"><a href="{{ route('home') }}" class="inline-block text-16 leading-16 white font-semibold">Home</a></li>
                                <li class="inline-block arrow text-16 leading-16 white font-normal"><i class="fa-solid fa-angle-right"></i></li>
                                <li class="inline-block text-16 leading-16 white font-normal">Thank You</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--=====CONTACT AREA START=======-->

    <div class="sp thank-you" style="background: var(--light-gray);">
        
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h2 class="text-44 font-semibold title1 leading-44 text-center">Thank you for completing the form. Our team will contact you shortly.</h2>

                    <div class="contact-time mt-5" style="background: #2773b714;padding: 30px;border-radius: 20px;">
                        <h4 class="text-center">Contact Information</h4>
                        <div class="row mt-4">
                            <div class="col-lg-4">
                                <div class="contact-box-p mt-30">
                                    <div class="icon">
                                        <img loading="lazy" src="{{ asset('assets/img/added/pin.svg') }}" alt="">
                                    </div>
                                    <div class="heading ml-16">
                                        <h3 class="text-20 font-semibold title1 leading-20">Location</h3>
                                        <a href="#" class="text-16 font-normal inline-block mt-3 pera1 leading-16 line-height-26">A-97, Sai Ashish Society, Behind Santosha Heights, Vadadla, Bharuch — 392011</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="contact-box-p mt-30">
                                    <div class="icon">
                                        <img loading="lazy" src="{{ asset('assets/img/added/tel.svg') }}" alt="">
                                    </div>
                                    <div class="heading ml-16">
                                        <h3 class="text-20 font-semibold title1 leading-20">Number</h3>
                                        <a href="tel:919904788479"
                                            class="text-16 font-normal inline-block mt-3 pera1 leading-16">+91 99047 88479</a>
                                            <br>
                                            <a href="tel:7698881819"
                                            class="text-16 font-normal inline-block mt-3 pera1 leading-16">+91 7698881819</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="contact-box-p mt-30">
                                    <div class="icon">
                                        <img loading="lazy" src="{{ asset('assets/img/added/email.svg') }}" alt="">
                                    </div>
                                    <div class="heading ml-16">
                                        <h3 class="text-20 font-semibold title1 leading-20">Mail</h3>
                                        <a href="mailto:srchemicalindustries9@gmail.com"
                                            class="text-16 font-normal inline-block mt-3 pera1 leading-16">srchemicalindustries9@gmail.com</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                        <a class="theme-btn2 ml-16 sm:ml-0 sm:mt-20" href="{{ route('contact') }}">Get Inquiry Now 
                            <span class="arrow1"><i class="fa-solid fa-arrow-right"></i></span>
                            <span class="arrow2"><i class="fa-solid fa-arrow-right"></i></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
