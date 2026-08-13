@extends('layouts.app')

@section('title', 'About SRCIL | SR Chemical Industries Limited | Chemical Solutions Leader')

@section('content')
    <style>
    body { background-image: url("{{ asset('assets/img/added/bg-1.svg') }}") !important; background-size: 350px !important; background-position: right top !important; background-repeat: no-repeat !important; background-color: #f4f8fd !important; }
    </style>

    <div class="common-hero" style="background-image: url('{{ asset('assets/img/added/COLOR.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="common-hero-heading">
                        <h1 class="text-60 sm:text-30 md:text-30 leading-56 font-semibold white">About Our Company</h1>
                        <div class="page-change">
                            <ul>
                                <li class="inline-block"><a href="{{ route('home') }}" class="inline-block text-16 leading-16 white font-semibold">Home</a></li>
                                <li class="inline-block arrow text-16 leading-16 white font-normal"><i class="fa-solid fa-angle-right"></i></li>
                                <li class="inline-block text-16 leading-16 white font-normal">About Us</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- About Us text section -->
    <div class="about-us-text-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-10">
                    <h2 class="about-green-heading" data-aos="fade-up" data-aos-duration="700">About Us.</h2>
                    <p>SR Chemical Industries Limited is a trusted supplier and distributor of industrial chemicals, specialty chemicals, solvents, inorganic phosphates, and raw materials. We serve diverse industries with a commitment to quality, reliability, and customer satisfaction.</p>
                    <p>With strong industry expertise and a customer-focused approach, we deliver consistent quality products, dependable supply solutions, and long-term value to businesses across India and international markets.</p>
                </div>
            </div>
        </div>
    </div>

    <!--=====SERVICE AREA START=======-->

    <div class="about-service bg1 sp _relative">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="heading4">
                        <h2 class="text-44 sm:text-30 md:text-30 leading-56 font-semibold title1 line-height-60">Why Choose SRCIL</h2>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="about-service-buttons">
                        <button class="service4-prev-arrow"><i class="fa-solid fa-angle-left"></i></button>
                        <button class="service4-next-arrow"><i class="fa-solid fa-angle-right"></i></button>
                    </div>
                </div>
            </div>

            <div class="border4"></div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="service4-slider-all mt-40">
                        <div class="about-service-single-slider">
                            
                            <div class="heading4 mt-20">
                                <h4>
                                    <a class="text-20 leading-20 font-semibold inline-block title1" href="#">Premium Quality Products</a>
                                </h4>
                                <p class="mt-16 text-16 font-normal pb-20 pera1 leading-26">We maintain strict quality standards to deliver reliable and industry-approved chemical solutions.</p>
                            </div>
                        </div>

                        <div class="about-service-single-slider">
                            <div class="heading4 mt-20">
                                <h4><a class="text-20 leading-20 font-semibold inline-block title1" href="#">Reliable Supply Chain</a></h4>
                                <p class="mt-16 text-16 font-normal pb-20 pera1 leading-26">Our strong sourcing and distribution network ensures uninterrupted product availability.</p>
                            </div>
                        </div>

                        <div class="about-service-single-slider">
                            <div class="heading4 mt-20">
                                <h4><a class="text-20 leading-20 font-semibold inline-block title1" href="#">Timely Delivery</a></h4>
                                <p class="mt-16 text-16 font-normal pb-20 pera1 leading-26">Efficient logistics and planning help us deliver products safely and on schedule..</p>
                            </div>
                        </div>

                        <div class="about-service-single-slider">
                            <div class="heading4 mt-20">
                                <h4><a class="text-20 leading-20 font-semibold inline-block title1" href="#">Global Trade Expertise</a></h4>
                                <p class="mt-16 text-16 font-normal pb-20 pera1 leading-26">Serving domestic and international markets with trusted trading and sourcing solutions.</p>
                            </div>
                        </div>

                        <div class="about-service-single-slider">
                            <div class="heading4 mt-20">
                                <h4><a class="text-20 leading-20 font-semibold inline-block title1" href="#">Customer-Centric Approach</a></h4>
                                <p class="mt-16 text-16 font-normal pb-20 pera1 leading-26">Building long-term partnerships through transparency, responsiveness, and dedicated support.</p>
                            </div>
                        </div>

                        <div class="about-service-single-slider">
                            <div class="heading4 mt-20">
                                <h4><a class="text-20 leading-20 font-semibold inline-block title1" href="#">Competitive Pricing</a></h4>
                                <p class="mt-16 text-16 font-normal pb-20 pera1 leading-26">We offer cost-effective solutions without compromising on product quality and service.</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <!--=====OUR VALUES AREA START=======-->
    <div class="mission-vision-section">
        <div class="mv-block mv-block-dark">
            <div class="container">
                <div class="row align-items-center g-4">
                    <div class="col-lg-6" data-aos="fade-right" data-aos-duration="800">
                        <div class="mv-text">
                            <div class="mv-badge"><i class="fa-solid fa-crosshairs"></i> Mission</div>
                            <h2 class="mv-heading">Mission</h2>
                            <p>To deliver high-quality chemical products and reliable supply solutions by connecting industries with superior raw materials through integrity, innovation, compliance, and customer-centric service excellence.</p>
                        </div>
                    </div>
                    <div class="col-lg-6" data-aos="fade-left" data-aos-duration="800">
                        <div class="mv-image-wrap">
                            <img loading="lazy" src="{{ asset('assets/img/added/cta-banner.jpg') }}" alt="Mission — Industrial Chemical Facility">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mv-block mv-block-light">
            <div class="container">
                <div class="row align-items-center g-4">
                    <div class="col-lg-6 order-lg-2" data-aos="fade-left" data-aos-duration="800">
                        <div class="mv-text">
                            <div class="mv-badge" style="background: var(--primary-blue);"><i class="fa-solid fa-eye"></i> Vision</div>
                            <h2 class="mv-heading">Vision</h2>
                            <p>To become a globally recognized chemical solutions provider by fostering sustainable growth, maintaining the highest quality standards, and building long-term partnerships based on trust, reliability, and innovation.</p>
                        </div>
                    </div>
                    <div class="col-lg-6 order-lg-1" data-aos="fade-right" data-aos-duration="800">
                        <div class="mv-image-wrap" style="border-color: rgba(15,82,134,0.15);">
                            <img loading="lazy" src="{{ asset('assets/img/added/Vision.jpg') }}" alt="Vision — Global Chemical Supply">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--=====OUR VALUES AREA END=======-->

    <!--=====PROCESS AREA START=======-->

    <div class="process sp _relative" style="background-color: #F1F5FD;">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 m-auto text-center">
                    <div class="heading1">
                        <h2 class="text-44 sm:text-30 md:text-30 leading-56 font-semibold title1">Our Core Services</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 d-none d-lg-block">
                    <div class="video-area1 _relative" style="background-image: url('{{ asset('assets/img/added/video-area1-bg.jpg') }}');">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="process-bottom-box text-center">
                        <div class="heading1">
                            <h4><a href="{{ route('products.index') }}" class="text-20 leading-20 font-semibold title1 inline-block mt-24">Chemical Supply Solutions</a></h4>
                            <p class="mt-16 text-16 font-normal pera1 leading-26">Providing high-quality industrial, specialty, and laboratory chemicals to meet the diverse needs of industries worldwide.</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="process-bottom-box text-center">
                        <div class="heading1">
                            <h4><a href="{{ route('contact') }}" class="text-20 leading-20 font-semibold title1 inline-block mt-24">Global Trade &amp; Distribution</a></h4>
                            <p class="mt-16 text-16 font-normal pera1 leading-26">Connecting businesses with reliable sourcing, trading, and distribution solutions across domestic and international markets.</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="process-bottom-box text-center">
                        <div class="heading1">
                            <h4><a href="{{ route('products.index') }}" class="text-20 leading-20 font-semibold title1 inline-block mt-24">Industrial Raw Material Supply</a></h4>
                            <p class="mt-16 text-16 font-normal pera1 leading-26">Supplying dependable industrial raw materials and products to support efficient manufacturing and business operations.</p>
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
