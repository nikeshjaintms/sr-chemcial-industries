@extends('layouts.app')

@section('title', 'Solvent & Chemical Products Catalog | SR Chemical Industries Limited | SRCIL')

@section('content')
    <style>
    body { background-image: url("{{ asset('assets/img/added/bg-1.svg') }}") !important; background-size: 350px !important; background-position: right top !important; background-repeat: no-repeat !important; background-color: #f4f8fd !important; }
    
    .price-list-section {
        padding: 60px 0;
    }
    .notes-container {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 40px;
        justify-content: center;
    }
    .note-badge {
        background: var(--primary-blue, #0F3A55);
        color: #fff;
        padding: 10px 22px;
        border-radius: 30px;
        font-size: 14px;
        font-weight: 600;
        box-shadow: 0 2px 10px rgba(15, 58, 85, 0.1);
        border: 1px solid rgba(255,255,255,0.1);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .note-badge.highlight {
        background: var(--brand-green, #67B346);
    }
    .search-container {
        max-width: 500px;
        margin: 0 auto 40px auto;
        position: relative;
    }
    .search-input {
        width: 100%;
        padding: 14px 20px 14px 50px;
        border: 2px solid #E2E8F0;
        border-radius: 14px;
        font-size: 16px;
        transition: all 0.3s ease;
        background: #fff;
        color: var(--dark-text, #1A2B3C);
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    }
    .search-input:focus {
        outline: none;
        border-color: var(--brand-green, #67B346);
        box-shadow: 0 0 0 3px rgba(103, 179, 70, 0.15);
    }
    .search-icon {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: #718096;
        font-size: 18px;
    }
    .table-container {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 6px 30px rgba(15, 82, 134, 0.08);
        overflow: hidden;
        margin-bottom: 30px;
    }
    .price-table {
        width: 100%;
        border-collapse: collapse;
    }
    .price-table th {
        background: var(--primary-blue, #0F3A55);
        color: #fff;
        padding: 18px 24px;
        font-size: 16px;
        font-weight: 600;
        text-align: left;
        border: none;
    }
    .price-table td {
        padding: 18px 24px;
        font-size: 15px;
        color: var(--dark-text, #1A2B3C);
        border-bottom: 1px solid #EDF2F7;
        vertical-align: middle;
    }
    .price-table tr:last-child td {
        border-bottom: none;
    }
    .price-table tr:hover {
        background: #F8FAFC;
        cursor: pointer;
    }
    .price-badge {
        background: rgba(103, 179, 70, 0.1);
        color: #4e8f33;
        font-weight: 700;
        padding: 6px 12px;
        border-radius: 6px;
        display: inline-block;
        font-size: 14px;
    }
    .make-badge {
        background: #EDF2F7;
        color: #4A5568;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 13px;
        display: inline-block;
    }
    .empty-search {
        padding: 50px;
        text-align: center;
        color: #718096;
        font-size: 16px;
        display: none;
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 6px 30px rgba(15, 82, 134, 0.08);
    }
    
    @media (max-width: 768px) {
        .price-table th, .price-table td {
            padding: 12px 16px;
            font-size: 14px;
        }
        .note-badge {
            padding: 8px 16px;
            font-size: 13px;
        }
    }
    </style>

    <!-- Hero -->
    <div class="common-hero" style="background-image: url('{{ asset('assets/img/added/COLOR.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="common-hero-heading">
                        <h1 class="text-60 sm:text-30 md:text-30 leading-56 font-semibold white">Solvent &amp; Chemical</h1>
                        <div class="page-change">
                            <ul>
                                <li class="inline-block"><a href="{{ route('home') }}" class="inline-block text-16 white font-semibold">Home</a></li>
                                <li class="inline-block arrow text-16 white font-normal"><i class="fa-solid fa-angle-right"></i></li>
                                <li class="inline-block text-16 white font-normal">Products</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="price-list-section">
        <div class="container">
            <!-- Highlighted Top Notes -->
            <div class="notes-container" data-aos="fade-up" data-aos-duration="600">
                <div class="note-badge highlight"><i class="fa-solid fa-circle-check"></i> READY STOCK</div>
            </div>

            <div class="row mb-2">
                <div class="col-lg-12 text-center" data-aos="fade-up" data-aos-duration="600" data-aos-delay="100">
                    <h3 class="font-semibold title1 mb-4">Solvent &amp; Chemical Products Catalog</h3>
                </div>
            </div>

            <!-- Search Input Bar -->
            <div class="search-container" data-aos="fade-up" data-aos-duration="600" data-aos-delay="150">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" id="searchInput" class="search-input" placeholder="Search any chemical product (e.g. Acetic Acid, Methanol, HCL, H2SO4, IPA, Caustic Soda Flakes)..." autocomplete="off">
            </div>

            <!-- Product Price Table -->
            <div class="table-responsive table-container" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">
                <table class="price-table" id="priceTable">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Brand / Make</th>
                            <th>Packing / Specification</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $p)
                            <tr class="product-row" data-id="{{ $p->id }}" onclick="window.location.href='{{ route('products.show', $p->slug) }}'">
                                <td class="product-name font-semibold"><a href="{{ route('products.show', $p->slug) }}" class="text-dark">{{ $p->name }}</a></td>
                                <td>
                                    <span class="make-badge">{{ $p->category ? $p->category->name : 'Chemicals' }}</span>
                                </td>
                                <td>{{ $p->brand ?? 'SR Chemical' }}</td>
                                <td>{{ $p->packaging ?? 'Ready Stock' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="empty-search" id="emptySearch">
                    <i class="fa-solid fa-box-open me-2"></i> No matching products found. Please try searching another product or chemical formula (e.g. HCL, H2SO4, IPA).
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    var searchTimer = null;

    $('#searchInput').on('keyup input', function() {
        var q = $.trim($(this).val());
        clearTimeout(searchTimer);

        if (!q) {
            $('.product-row').show();
            $('#emptySearch').hide();
            return;
        }

        searchTimer = setTimeout(function() {
            $.getJSON('{{ url('/api/products/search') }}?q=' + encodeURIComponent(q), function(res) {
                if (res.status === 'success') {
                    if (!res.products || res.products.length === 0) {
                        $('.product-row').hide();
                        $('#emptySearch').show();
                        return;
                    }

                    var matchedIds = res.products.map(function(p) { return p.id; });
                    
                    $('.product-row').each(function() {
                        var rowId = parseInt($(this).data('id') || 0);
                        if (matchedIds.indexOf(rowId) !== -1) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });

                    if ($('.product-row:visible').length === 0) {
                        $('#emptySearch').show();
                    } else {
                        $('#emptySearch').hide();
                    }
                }
            });
        }, 150);
    });
});
</script>
@endpush
