<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-BJ1R1DR1X6"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());
        gtag('config', 'G-BJ1R1DR1X6');
    </script>
    <meta name="google-site-verification" content="ZMM32oJD-s2L3wJgCri3NOyFcJqGmsBhz-An2cnFsrA" />

    <title>@yield('title', 'SRCIL | SR Chemical Industries Limited | Industrial & Specialty Chemical Solutions')</title>

    <meta name="title" content="@yield('meta_title', 'SRCIL | SR Chemical Industries Limited | Industrial & Specialty Chemical Solutions')">
    <meta name="description" content="@yield('meta_description', 'SRCIL (SR Chemical Industries Limited) is a trusted supplier of industrial chemicals, specialty chemicals, solvents, and raw materials, delivering quality products and reliable solutions to industries worldwide.')">
    <meta name="keywords" content="Industrial Chemicals, Specialty Chemicals, Chemical Supplier, Chemical Trading Company, Chemical Exporter, Chemical Distributor, Industrial Raw Materials, Chemical Solutions, SR Chemical Industries Limited, SRCIL">
    <meta name="author" content="SR Chemical Industries Limited (SRCIL)">

    <!-- FAB ICON -->
    <link rel="shortcut icon" href="{{ asset('assets/img/added/favicon.png') }}" type="image/png">
    <link rel="icon" href="{{ asset('assets/img/added/favicon.png') }}" type="image/png">

    <!-- Preload critical resources -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="image" href="{{ asset('assets/img/added/MAIN.png') }}">
    <link rel="preload" as="image" href="{{ asset('assets/img/added/blue-logo.png') }}">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/nice-select.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/slick-slider.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/aos.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/swiper@12.1.2/swiper-bundle.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/mobile-menu.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/utility.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">

    <!-- JQUERY -->
    <script src="{{ asset('assets/js/jquery-3-7-1.min.js') }}"></script>

    <style>
        /* Custom Modern Pagination Styling */
        .pagination {
            display: flex !important;
            padding-left: 0 !important;
            list-style: none !important;
            border-radius: 12px !important;
            gap: 6px !important;
            justify-content: center !important;
            align-items: center !important;
            margin: 24px 0 !important;
        }
        .pagination svg, .pagination img {
            width: 14px !important;
            height: 14px !important;
            max-width: 14px !important;
            max-height: 14px !important;
            vertical-align: middle !important;
        }
        .page-item .page-link {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-width: 40px !important;
            height: 40px !important;
            padding: 0 14px !important;
            font-size: 14px !important;
            font-weight: 600 !important;
            color: #0F3A55 !important;
            background-color: #ffffff !important;
            border: 1px solid #E2E8F0 !important;
            border-radius: 10px !important;
            transition: all 0.25s ease !important;
            text-decoration: none !important;
            box-shadow: 0 2px 5px rgba(0,0,0,0.03) !important;
        }
        .page-item.active .page-link {
            background-color: #0F3A55 !important;
            border-color: #0F3A55 !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(15, 58, 85, 0.25) !important;
        }
        .page-item .page-link:hover {
            background-color: #67B346 !important;
            border-color: #67B346 !important;
            color: #ffffff !important;
            transform: translateY(-2px) !important;
        }
        .page-item.disabled .page-link {
            color: #94A3B8 !important;
            background-color: #F8FAFC !important;
            border-color: #E2E8F0 !important;
            cursor: not-allowed !important;
            transform: none !important;
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Header Partial -->
    @include('partials.header')

    <!-- Main Content -->
    @yield('content')

    <!-- Footer Partial -->
    @include('partials.footer')

    <!-- JS Scripts -->
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/fontawesome.js') }}"></script>
    <script src="{{ asset('assets/js/mobile-menu.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.magnific-popup.js') }}"></script>
    <script src="{{ asset('assets/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.countup.js') }}"></script>
    <script src="{{ asset('assets/js/slick-slider.js') }}"></script>
    <script src="{{ asset('assets/js/circle-progress.js') }}" defer></script>
    <script src="{{ asset('assets/js/jquery.nice-select.js') }}"></script>
    <script src="{{ asset('assets/js/gsap.min.js') }}" defer></script>
    <script src="{{ asset('assets/js/ScrollTrigger.min.js') }}" defer></script>
    <script src="{{ asset('assets/js/swiper-bundle.js') }}" defer></script>
    <script src="{{ asset('assets/js/Splitetext.js') }}" defer></script>
    <script src="{{ asset('assets/js/text-animation.js') }}" defer></script>
    <script src="{{ asset('assets/js/aos.js') }}"></script>
    <script src="{{ asset('assets/js/SmoothScroll.js') }}" defer></script>
    <script src="{{ asset('assets/js/jaquery-ripples.js') }}" defer></script>
    <script src="{{ asset('assets/js/jquery.lineProgressbar.js') }}" defer></script>
    <script src="{{ asset('assets/js/animation.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>

    @stack('scripts')
</body>
</html>
