@extends('layouts.app')

@section('title', 'Contact SRCIL | SR Chemical Industries Limited | Inquire & Support')

@section('content')
    <div class="common-hero" style="background: url('{{ asset('assets/img/bg/research-hero-bg.jpg') }}') no-repeat center center; background-size: cover;">
        <div class="container">
            <div class="row">
                <div class="col-lg-10">
                    <div class="common-hero-heading">
                        <h1 class="text-60 sm:text-30 md:text-30 leading-56 font-semibold white">Contact Us</h1>
                        <div class="page-change">
                            <ul>
                                <li class="inline-block"><a href="{{ route('home') }}" class="inline-block text-16 leading-16 white font-semibold">Home</a></li>
                                <li class="inline-block arrow text-16 leading-16 white font-normal"><i class="fa-solid fa-angle-right"></i></li>
                                <li class="inline-block text-16 leading-16 white font-normal">Contact Us</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--=====CONTACT AREA START=======-->

    <div class="sp" style="background: var(--light-gray);">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <h2 class="text-44 font-semibold title1 leading-44">Get in Touch !!</h2>

                    <div class="contact-info-single-box mt-30">

                        <div class="contact-info-item">
                            <div class="icon">
                                <img src="{{ asset('assets/img/added/pin.svg') }}" alt="contact">
                            </div>
                            <div class="heading ml-16">
                                <h3 class="text-20 font-semibold title1 leading-20">Location</h3>
                                <a href="#" class="text-16 font-normal inline-block mt-3 pera1 leading-16 line-height-26">A-97 Sai Ashish,NH-8 vadadla Bharuch 392011</a>
                            </div>
                        </div>

                        <div class="contact-info-divider"></div>

                        <div class="contact-info-item">
                            <div class="icon">
                                <img src="{{ asset('assets/img/added/tel.svg') }}" alt="contact">
                            </div>
                            <div class="heading ml-16">
                                <h3 class="text-20 font-semibold title1 leading-20">Number</h3>
                                <a href="tel:7600181931" class="text-16 font-normal inline-block mt-3 pera1 leading-16">+91 76001 81931</a>
                                <br>
                                <a href="tel:7041553966" class="text-16 font-normal inline-block mt-3 pera1 leading-16">+91 70415 53966</a>
                            </div>
                        </div>

                        <div class="contact-info-divider"></div>

                        <div class="contact-info-item">
                            <div class="icon">
                                <img src="{{ asset('assets/img/added/email.svg') }}" alt="contact">
                            </div>
                            <div class="heading ml-16">
                                <h3 class="text-20 font-semibold title1 leading-20">Mail</h3>
                                <a href="mailto:srchemicalindustries9@gmail.com" class="text-16 font-normal inline-block mt-3 pera1 leading-16">srchemicalindustries9@gmail.com</a>
                                <a href="mailto:marketing@srchemicalindustries.com" class="text-16 font-normal inline-block mt-3 pera1 leading-16">marketing@srchemicalindustries.com</a>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="contact-form sm:mt-30 md:mt-30">
                        <form id="contactForm" action="{{ route('contact.send') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="single-input">
                                        <label>First Name*</label>
                                        <input id="form_name" type="text" name="name" placeholder="First Name" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="single-input">
                                        <label>Last Name*</label>
                                        <input id="form_last_name" type="text" name="last_name" placeholder="Last Name">
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="single-input mt-20">
                                        <label>Email Address*</label>
                                        <input id="form_email" type="email" name="email" placeholder="Email" required>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="single-input mt-20">
                                        <label>Phone Number*</label>
                                        <input id="form_phone" type="text" name="phone" placeholder="Phone Number" required>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="single-input mt-20">
                                        <label>Message*</label>
                                        <textarea id="form_message" rows="5" name="message" placeholder="Type your message..." required></textarea>
                                    </div>
                                    <div class="mt-20">
                                        <button class="theme-btn1" type="submit">Send Now 
                                            <span class="arrow1"><i class="fa-solid fa-arrow-right"></i></span>
                                            <span class="arrow2"><i class="fa-solid fa-arrow-right"></i></span>
                                        </button>
                                    </div>
                                </div>

                            </div>
                            <div id="msgSubmit" class="hidden mt-3"></div>
                        </form>
                    </div>

                </div>
            </div>
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
    $(document).ready(function () {
        $("#contactForm").submit(function (event) {
            event.preventDefault();
    
            $.ajax({
                type: "POST",
                url: "{{ route('contact.send') }}",
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    $("#msgSubmit")
                        .html("Message sent successfully!")
                        .removeClass("hidden alert-danger")
                        .addClass("alert alert-success")
                        .fadeIn();
    
                    setTimeout(function () {
                        window.location.href = "{{ route('thank-you') }}";
                    }, 1000);
                },
                error: function () {
                    $("#msgSubmit")
                        .html("Something went wrong!")
                        .removeClass("hidden alert-success")
                        .addClass("alert alert-danger")
                        .fadeIn();
                }
            });
        });
    });
</script>
@endpush
