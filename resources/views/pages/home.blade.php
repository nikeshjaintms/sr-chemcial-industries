@extends('layouts.app')

@section('title', 'SRCIL | SR Chemical Industries Limited | Industrial & Specialty Chemical Solutions')

@section('content')
    <div class="hero1-slider">
        <div class="hero1-single-slider hero1-bg1 flex items-center md:inline-block sm:inline-block _relative">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-7">
                        <div class="main-heading1 mt-60">
                            <h1 class="md:text-40 text-60 font-semibold white">Global Trade, <span style="color:#67B346;">Pure Quality,</span> Trusted Partnerships. </h1>
                            <p class="mt-24 text-16 leading-26 white font-normal">Connecting industries worldwide through reliable chemical sourcing, uncompromising quality standards, and long-term business relationships built on trust, and commitment.</p>
                            <div class="buttons mt-30">
                                <a class="theme-btn1" href="{{ route('about') }}">Know More
                                    <span class="arrow1"><i class="fa-solid fa-arrow-right"></i></span>
                                    <span class="arrow2"><i class="fa-solid fa-arrow-right"></i></span>
                                </a>
                                <a class="theme-btn2" href="{{ route('contact') }}">Get Inquiry
                                    <span class="arrow1"><i class="fa-solid fa-arrow-right"></i></span>
                                    <span class="arrow2"><i class="fa-solid fa-arrow-right"></i></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Hero Stats Bar -->
            <div class="hero-stats-bar d-none d-lg-block">
                <div class="container">
                    <div class="row g-3">
                        <div class="col-lg-3 col-md-6">
                            <div>
                                <div class="stat-number">20+</div>
                                <div class="stat-label">Global Trade Partners</div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div>
                                <div class="stat-number">15+</div>
                                <div class="stat-label">Product Categories</div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div>
                                <div class="stat-number">100+</div>
                                <div class="stat-label">Products Available</div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div>
                                <div class="stat-number">Pan India</div>
                                <div class="stat-label">Supply Network</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>

    <!--=====ABOUT AREA START=======-->

    <div class="about1 sp">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-5">
                    <div class="about1-images">
                        <div class="image1 absolute image-anime reveal top-0 left-0">
                            <img loading="lazy" src="{{ asset('assets/img/added/HM.png') }}" alt="Chemical laboratory — SRCIL">
                        </div>
                        <div class="image2 absolute right-0 top-90 image-anime reveal">
                            <img loading="lazy" src="{{ asset('assets/img/added/about-1.png') }}" alt="Industrial chemical processing facility">
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="heading1 pl-50 md:pl-0 xs:pl-0 md:pt-30 sm:pt-30">
                        <span class="span1 text-18 leading-18 title1 font-normal mb-16" data-aos="fade-left" data-aos-duration="700">About SRCIL</span>
                        <h2 class="text-44 sm:text-30 md:text-30 leading-56 font-semibold title1 text-anime-style-3">SRCIL Commitment You Can Trust.</h2>
                        <p class="mt-16 text-16 font-normal pera1 leading-26" data-aos-delay="200" data-aos="fade-left" data-aos-duration="800">We are a trusted chemical trading and industrial solutions company serving domestic and international markets with reliability, quality, and integrity. With a strong presence across industries, SRCIL specializes in the supply of Industrial Chemicals, Specialty Chemicals, Raw Materials, Laboratory Chemicals, Solvents, and Industrial Products, ensuring consistent quality and dependable service for our global partners.</p>

                        <div class="row" data-aos="fade-left" data-aos-duration="1200" data-aos-delay="200">
                            <div class="col-md-5">
                                <div class="counter-box pt-30">
                                    <h3 class="text-44 sm:text-30 leading-44 font-semibold title1">20+</h3>
                                    <p class="pt-10 text-16 font-normal pera1 leading-26">Global Trade Partners</p>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="counter-box pt-30">
                                    <h3 class="text-44 sm:text-30 leading-44 font-semibold title1">15+</h3>
                                    <p class="pt-10 text-16 font-normal pera1 leading-26">Product Categories</p>
                                </div>
                            </div>
                            <div class="col-md-2">
                            </div>
                        </div>
                        <div class="button mt-30" data-aos="fade-left" data-aos-duration="800" data-aos-delay="200">
                            <a class="theme-btn3" href="{{ route('contact') }}">Send Inquiry
                                <span class="arrow1"><i class="fa-solid fa-arrow-right"></i></span>
                                <span class="arrow2"><i class="fa-solid fa-arrow-right"></i></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--=====ABOUT AREA END=======-->

    <!--=====SERVICE AREA START=======-->

    <div class="service sp" style="background: #F1F5FD;">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 m-auto text-center">
                    <div class="heading1">
                        <span class="span1 text-18 leading-18 title1 font-normal mb-16" data-aos="fade-left"> Our Service</span>
                        <h2 class="text-44 sm:text-30 md:text-30 leading-56 font-semibold title1 text-anime-style-3">Empowering Industries Through Quality Products & Trusted Supply</h2>
                    </div>
                </div>
            </div>
            <div class="space30"></div>

           <div class="row justify-content-center">

    <div class="col-lg-5 col-md-6" data-aos="zoom-in-up" data-aos-duration="1200" data-aos-delay="300">
        <div class="service-box1 mt-30">
            <div class="image image-anime">
                <img loading="lazy" class="w-full" src="{{ asset('assets/img/added/Chemical Supply Solutions.jpg') }}" alt="Chemical Supply Solutions">
            </div>
            <div class="heading1">
                <h4>
                    <a href="{{ route('products.category', 'chlor-alkali-chemicals') }}" class="text-20 leading-20 font-semibold title1">
                        Chemical Supply Solutions
                    </a>
                </h4>
                <p class="mb-20 mt-16 text-16 font-normal pera1 leading-26">
                    We provide high-quality industrial and specialty chemicals to various industries, ensuring consistent quality, competitive pricing, and reliable supply.
                </p>
                <a href="{{ route('products.index') }}" class="learn text-16 leading-16 font-semibold title1">
                    Read More
                    <span class="arrow1"><i class="fa-solid fa-arrow-right"></i></span>
                    <span class="arrow2"><i class="fa-solid fa-arrow-right"></i></span>
                </a>
            </div>
        </div>
    </div>

    <div class="col-lg-5 col-md-6" data-aos="zoom-in-up" data-aos-duration="1000" data-aos-delay="300">
        <div class="service-box1 mt-30">
            <div class="image image-anime">
                <img loading="lazy" class="w-full" src="{{ asset('assets/img/added/Global Trade & Distribution.png') }}" alt="Global Trade and Distribution">
            </div>
            <div class="heading1">
                <h4>
                    <a href="{{ route('contact') }}" class="text-20 leading-20 font-semibold title1">
                        Global Trade &amp; Distribution
                    </a>
                </h4>
                <p class="mb-20 mt-16 text-16 font-normal pera1 leading-26">
                    Leveraging our international network and market expertise, we facilitate smooth import and export operations across domestic and global markets.
                </p>
                <a href="{{ route('contact') }}" class="learn text-16 leading-16 font-semibold title1">
                    Read More
                    <span class="arrow1"><i class="fa-solid fa-arrow-right"></i></span>
                    <span class="arrow2"><i class="fa-solid fa-arrow-right"></i></span>
                </a>
            </div>
        </div>
    </div>

    </div>
        </div>
    </div>

    <!--=====SERVICE AREA END=======-->

    <!--=====TESTIMONIAL AREA START=======-->

    <div class="test1 sp" style="background: #fff;">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 m-auto text-center">
                    <div class="heading1">
                        <span class="span1 text-18 leading-18 title1 font-normal mb-16" data-aos="fade-left" data-aos-duration="700">Testimonials</span>
                        <h2 class="text-44 sm:text-30 md:text-30 leading-56 font-semibold title1 text-anime-style-3">What Our Clients &amp; Business Partners Say</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="tes1-slider-all srcil-tes-slider" data-aos-delay="300" data-aos="fade-up" data-aos-duration="1000">

                        <div class="single-slider1 srcil-tes-card">
                            <div class="srcil-tes-inner">
                                <div class="srcil-tes-quote">&ldquo;</div>
                                <p class="srcil-tes-text">&ldquo;SRCIL has consistently delivered high-quality chemical products on time. Their professionalism and commitment to customer satisfaction make them a trusted business partner.&rdquo;</p>
                                <div class="srcil-tes-footer"><div class="srcil-tes-footer-inner testimonial-pill"><div class="srcil-tes-name">Reliable Supply</div><div class="srcil-tes-sub">Chemical Solutions</div></div></div>
                            </div>
                        </div>

                        <div class="single-slider1 srcil-tes-card">
                            <div class="srcil-tes-inner">
                                <div class="srcil-tes-quote">&ldquo;</div>
                                <p class="srcil-tes-text">The product quality and technical support provided by SRCIL have exceeded our expectations. We highly value their dedication to maintaining industry standards.</p>
                                <div class="srcil-tes-footer"><div class="srcil-tes-footer-inner testimonial-pill"><div class="srcil-tes-name">Quality Assurance</div><div class="srcil-tes-sub">Trusted Products</div></div></div>
                            </div>
                        </div>

                        <div class="single-slider1 srcil-tes-card">
                            <div class="srcil-tes-inner">
                                <div class="srcil-tes-quote">&ldquo;</div>
                                <p class="srcil-tes-text">&ldquo;Working with SRCIL has been a seamless experience. Their responsive team and efficient service have helped us maintain uninterrupted operations.&rdquo;</p>
                                <div class="srcil-tes-footer"><div class="srcil-tes-footer-inner testimonial-pill"><div class="srcil-tes-name">Professional Service</div><div class="srcil-tes-sub">Customer Focused</div></div></div>
                            </div>
                        </div>

                        <div class="single-slider1 srcil-tes-card">
                            <div class="srcil-tes-inner">
                                <div class="srcil-tes-quote">&ldquo;</div>
                                <p class="srcil-tes-text">&ldquo;SRCIL&rsquo;s expertise in sourcing and international trade has helped us expand our business with confidence and reliability.&rdquo;</p>
                                <div class="srcil-tes-footer"><div class="srcil-tes-footer-inner testimonial-pill"><div class="srcil-tes-name">Global Trade</div><div class="srcil-tes-sub">Business Growth</div></div></div>
                            </div>
                        </div>

                        <div class="single-slider1 srcil-tes-card">
                            <div class="srcil-tes-inner">
                                <div class="srcil-tes-quote">&ldquo;</div>
                                <p class="srcil-tes-text">&ldquo;We appreciate SRCIL&rsquo;s commitment to timely deliveries and transparent communication. They are a dependable partner.&rdquo;</p>
                                <div class="srcil-tes-footer"><div class="srcil-tes-footer-inner testimonial-pill"><div class="srcil-tes-name">Timely Delivery</div><div class="srcil-tes-sub">Supply Excellence</div></div></div>
                            </div>
                        </div>

                        <div class="single-slider1 srcil-tes-card">
                            <div class="srcil-tes-inner">
                                <div class="srcil-tes-quote">&ldquo;</div>
                                <p class="srcil-tes-text">&ldquo;SRCIL values long-term relationships and always strives to provide the best solutions. Their integrity and professionalism set them apart.&rdquo;</p>
                                <div class="srcil-tes-footer"><div class="srcil-tes-footer-inner testimonial-pill"><div class="srcil-tes-name">Trusted Partnership</div><div class="srcil-tes-sub">Long-Term Success</div></div></div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--=====TESTIMONIAL AREA END=======-->

    <!--=====WHY CHOOSE US AREA START=======-->

    <div class="wcu-section">
        <div class="container">

            <!-- Section Title -->
            <h2 class="wcu-title">WHY CHOOSE US</h2>

            <!-- Main Layout: Left | Center | Right -->
            <div class="wcu-layout">

                <!-- LEFT COLUMN -->
                <div class="wcu-col wcu-col-left">

                    <div class="wcu-feature wcu-feature-left">
                        <div class="wcu-text wcu-text-right">
                            <h4>Wide Product Range</h4>
                            <p>We offer a wide product range to meet diverse industry needs.</p>
                        </div>
                        <div class="wcu-icon-circle">
                            <i class="fa-solid fa-atom"></i>
                        </div>
                    </div>

                    <div class="wcu-feature wcu-feature-left">
                        <div class="wcu-text wcu-text-right">
                            <h4>Timely Delivery</h4>
                            <p>We ensure timely delivery for seamless and efficient operations.</p>
                        </div>
                        <div class="wcu-icon-circle">
                            <i class="fa-solid fa-flask"></i>
                        </div>
                    </div>

                    <div class="wcu-feature wcu-feature-left">
                        <div class="wcu-text wcu-text-right">
                            <h4>Quality Assurance</h4>
                            <p>We ensure strict quality assurance for reliable chemicals.</p>
                        </div>
                        <div class="wcu-icon-circle">
                            <i class="fa-solid fa-microscope"></i>
                        </div>
                    </div>

                </div>

                <!-- CENTER CIRCLE IMAGE -->
                <div class="wcu-col wcu-col-center">
                    <div class="wcu-center-circle">
                        <img src="{{ asset('assets/img/added/why.png') }}" alt="SR Chemical Industries Limited">
                    </div>
                </div>

                <!-- RIGHT COLUMN -->
                <div class="wcu-col wcu-col-right">

                    <div class="wcu-feature wcu-feature-right">
                        <div class="wcu-icon-circle">
                            <i class="fa-solid fa-atom"></i>
                        </div>
                        <div class="wcu-text wcu-text-left">
                            <h4>Experienced Team</h4>
                            <p>Our experienced team delivers expert solutions and service.</p>
                        </div>
                    </div>

                    <div class="wcu-feature wcu-feature-right">
                        <div class="wcu-icon-circle">
                            <i class="fa-solid fa-flask"></i>
                        </div>
                        <div class="wcu-text wcu-text-left">
                            <h4>Customer-Centric Approach</h4>
                            <p>We focus on a customer-centric approach to ensure satisfaction.</p>
                        </div>
                    </div>

                    <div class="wcu-feature wcu-feature-right">
                        <div class="wcu-icon-circle">
                            <i class="fa-solid fa-microscope"></i>
                        </div>
                        <div class="wcu-text wcu-text-left">
                            <h4>Strategic Locations</h4>
                            <p>Our strategic locations ensure efficient distribution.</p>
                        </div>
                    </div>

                </div>

            </div><!-- /.wcu-layout -->

        </div>
    </div>

    <!--=====WHY CHOOSE US AREA END=======-->

    <!--=====CTA AREA START=======-->

    <div class="cta1 sp" style="background: linear-gradient(rgba(13, 39, 68, 0.85), rgba(0, 0, 0, 0.6)),
            url('{{ asset('assets/img/added/cta-banner.jpg') }}');
background-position: center;
background-size: cover;
background-repeat: no-repeat;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-5">
                    <h2 class="text-44 sm:text-30 md:text-30 leading-56 font-semibold white">Ready to Partner for Quality Chemical Solutions?</h2>
                </div>
                <div class="col-lg-7">
                    <div class="buttons text-end md:text-start xs:text-start sm:mt-20 md:mt-20">
                        <a class="theme-btn1" href="{{ route('contact') }}">Contact Us Now <span class="arrow1"><i
                                    class="fa-solid fa-arrow-right"></i></span><span class="arrow2"><i
                                    class="fa-solid fa-arrow-right"></i></span></a>
                        <a class="theme-btn2 ml-16 sm:ml-0 sm:mt-20" href="{{ route('contact') }}">Get Inquiry Now <span
                                class="arrow1"><i class="fa-solid fa-arrow-right"></i></span><span class="arrow2"><i
                                    class="fa-solid fa-arrow-right"></i></span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--=====CTA AREA END=======-->
@endsection
