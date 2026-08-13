<div class="paginacontainer">

    <div class="progress-wrap">
        <i class="fa-solid fa-arrow-up"></i>
        <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
        </svg>
    </div>

</div>

<style>
.main-menu-ex ul li .sub-menu {
    border-radius: 10px !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
    max-height: 380px !important;
    overflow-y: auto !important;
    overflow-x: hidden !important;
    scrollbar-width: thin !important;
    scrollbar-color: #0F5286 #F1F5F9 !important;
}

.main-menu-ex ul li .sub-menu::-webkit-scrollbar {
    width: 5px !important;
}

.main-menu-ex ul li .sub-menu::-webkit-scrollbar-track {
    background: #F1F5F9 !important;
    border-radius: 10px !important;
}

.main-menu-ex ul li .sub-menu::-webkit-scrollbar-thumb {
    background: #0F5286 !important;
    border-radius: 10px !important;
}

.main-menu-ex ul li .sub-menu::-webkit-scrollbar-thumb:hover {
    background: #1D4ED8 !important;
}
</style>

<header>

    <div class="header-area header-area1 header-area-all d-none d-lg-block" id="header">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="header-elements">
                        <div class="site-logo">
                            <a href="{{ route('home') }}">
                                <img src="{{ asset('assets/img/added/blue-logo.png') }}" alt="SR Chemical Industries Limited">
                            </a>
                        </div>
                        <div class="main-menu-ex main-menu-ex1">
                            <ul>
                                <li><a href="{{ route('home') }}">Home</a></li>

                                <li class="dropdown-menu-parrent"><a href="#" class="main1">About Us <i class="fa-solid fa-angle-down"></i></a>
                                    <ul>
                                        <li><a href="{{ route('about') }}">Overview</a></li>
                                        <li><a href="{{ route('certificate') }}">Certificate</a></li>

                                    </ul>
                                </li>

                                 <li class="dropdown-menu-parrent"><a href="{{ route('products.index') }}" class="main1">Products <i class="fa-solid fa-angle-down"></i></a>
                                     <ul>
                                         @if(isset($menuCategories))
                                             @foreach($menuCategories as $menuCat)
                                                 @include('partials.menu-item', ['category' => $menuCat])
                                             @endforeach
                                         @endif
                                     </ul>
                                 </li>
                                <li><a href="{{ route('clients') }}">Clients</a></li>
                                <li><a href="{{ route('contact') }}">Contact</a></li>
                            </ul>
                        </div>
                        <div class="header1-buttons">
                            <a class="theme-btn1" href="{{ route('contact') }}">Business Inquiry
                                <span class="arrow1"><i class="fa-solid fa-arrow-right"></i></span>
                                <span class="arrow2"><i class="fa-solid fa-arrow-right"></i></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<div class="mobile-header mobile-header-main d-block d-lg-none ">
    <div class="container-fluid">
        <div class="col-12">
            <div class="mobile-header-elements">
                <div class="mobile-logo">
                    <a href="{{ route('home') }}"><img src="{{ asset('assets/img/added/blue-logo.png') }}" alt="SR Chemical Industries Limited"></a>
                </div>
                <div class="mobile-nav-icon">
                    <i class="fa-duotone fa-bars-staggered"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mobile-sidebar d-block d-lg-none">
    <div class="logo-m">
        <a href="{{ route('home') }}"><img src="{{ asset('assets/img/added/blue-logo.png') }}" alt="SR Chemical Industries Limited"></a>
    </div>
    <div class="menu-close">
        <i class="fa-solid fa-xmark"></i>
    </div>
    <div class="mobile-nav">
        <ul>
            <li><a href="{{ route('home') }}">Home</a></li>
            <li class="has-dropdown"><a href="#">About Us</a>
                <ul class="sub-menu">
                    <li><a href="{{ route('about') }}">Overview</a></li>
                    <li><a href="{{ route('certificate') }}">Certificate</a></li>
                </ul>
            </li>

            <li class="has-dropdown"><a href="{{ route('products.index') }}">Products</a>
                <ul class="sub-menu">
                    @if(isset($menuCategories))
                        @foreach($menuCategories as $menuCat)
                            @include('partials.mobile-menu-item', ['category' => $menuCat])
                        @endforeach
                    @endif
                </ul>
            </li>
            <li><a href="{{ route('clients') }}">Clients</a></li>
            <li><a href="{{ route('contact') }}">Contact Us</a></li>
        </ul>

        <div class="mobile-button">
            <a class="theme-btn3" href="{{ route('contact') }}">Business Inquiry <i class="fa-solid fa-arrow-right"></i></a>
        </div>

        <div class="footer-contact-area1 md:pl-0 pl-20 sm:pl-0 mt-30">
            <h3 class="text-24 leading-26 font-semibold title1 pb-10">Get in touch</h3>
            <div class="contact-box d-flex">
                <div class="icon">
                    <i class="fa-solid fa-envelope" style="color:#67B346; font-size:18px;"></i>
                </div>
                <div class="text">
                    <a href="mailto:srchemicalindustries9@gmail.com">srchemicalindustries9@gmail.com</a>
                </div>
            </div>

            <div class="contact-box d-flex">
                <div class="icon">
                    <i class="fa-solid fa-location-dot" style="color:#67B346; font-size:18px;"></i>
                </div>
                <div class="text">
                    <a href="#">A-97, Sai Ashish Society, Behind Santosha Heights, Vadadla, Bharuch — 392011</a>
                </div>
            </div>

            <div class="contact-box d-flex">
                <div class="icon">
                    <i class="fa-solid fa-phone" style="color:#67B346; font-size:18px;"></i>
                </div>
                <div class="text">
                    <a href="tel:+917600181931">+91 76001 81931</a><br>
                    <a href="tel:+917041553966">+91 70415 53966</a>
                </div>
            </div>

        </div>

        <div class="contact-infos">
            <h3>Our Location</h3>
            <ul class="social-icon">
                <li><a href="https://www.linkedin.com/company/sr-chemical-industries-limited/" target="_blank" rel="noopener"><i class="fa-brands fa-linkedin-in"></i></a></li>
                <li><a href="#"><i class="fa-brands fa-x-twitter"></i></a></li>
                <li><a href="#"><i class="fa-brands fa-youtube"></i></a></li>
                <li><a href="#"><i class="fa-brands fa-instagram"></i></a></li>
            </ul>
        </div>

    </div>
</div>
