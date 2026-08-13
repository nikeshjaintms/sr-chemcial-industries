<div class="footer1">
    <div class="container">
        <div class="row g-4" style="align-items:flex-start;">

            <!-- Logo + tagline + social -->
            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="logo-area">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('assets/img/added/blue-logo.png') }}" alt="SR Chemical Industries Limited" style="height:50px;width:auto;">
                    </a>
                    <p style="font-size:13px;color:rgba(255,255,255,0.62);line-height:1.75;margin-top:14px;">
                        Quality Chemicals. Global Reach.<br>
                        Empowering industries with reliable sourcing, efficient distribution, and uncompromising quality standards.
                    </p>
                    <ul class="footer-social-area1 mt-20">
                        <li><a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a></li>
                        <li><a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a></li>
                        <li><a href="https://www.linkedin.com/company/sr-chemical-industries-limited/" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a></li>
                    </ul>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-4 col-md-3 col-sm-6">
                <h3 class="footer-col-heading">Quick Links</h3>
                <ul class="footer-list1">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li><a href="{{ route('about') }}">About Us</a></li>
                    <li><a href="{{ route('clients') }}">Our Clients</a></li>
                    <li><a href="{{ route('products.index') }}">Products</a></li>
                    <li><a href="{{ route('contact') }}">Contact Us</a></li>
                </ul>
            </div>

            <!-- Get In Touch -->
            <div class="col-lg-4 col-md-12 col-sm-12">
                <h3 class="footer-col-heading">Get In Touch</h3>
                <div class="contact-section">
                    <div><img loading="lazy" src="{{ asset('assets/img/added/email.svg') }}" alt="Email"></div>
                    <div><p><a href="mailto:srchemicalindustries9@gmail.com"><b>srchemicalindustries9@gmail.com</b></a></p>
                    <p><a href="mailto:marketing@srchemicalindustries.com"><b>marketing@srchemicalindustries.com</b></a></p></div>

                </div>
                <div class="contact-section">
                    <div><img loading="lazy" src="{{ asset('assets/img/added/pin.svg') }}" alt="Address"></div>
                    <div><p><a href="#"><b>A-97 Sai Ashish,NH-8 vadadla Bharuch 392011</b></a></p></div>
                </div>
                <div class="contact-section">
                    <div><img loading="lazy" src="{{ asset('assets/img/added/tel.svg') }}" alt="Phone"></div>
                    <div>
                        <p><a href="tel:+917600181931"><b>+91 76001 81931</b></a></p>
                        <p><a href="tel:+917041553966"><b>+91 70415 53966</b></a></p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Copyright Row -->
        <div class="row align-items-center coppy-right1">
            <div class="col-lg-8 col-md-8 col-sm-12">
                <p>© Copyright 2026 SR Chemical Industries Limited. All rights reserved. Designed &amp; Developed by <a href="https://www.techomaxsolution.com/" target="_blank" rel="noopener noreferrer"><b>Techomax Solution</b></a></p>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="conditons-area1">
                    <a href="#"><b>Terms &amp; Conditions</b></a>
                    <a href="#"><b>Privacy Policy</b></a>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- SR Chemicals AI Chatbot Assets -->
<link rel="stylesheet" href="{{ asset('assets/css/chatbot.css') }}?v={{ time() }}">
<script src="{{ asset('assets/js/chatbot.js') }}?v={{ time() }}"></script>
