@php
    $categoryDisplayName = $category->name;
    if ($category->slug === 'gnfc-products' || $category->slug === 'gnfc') {
        $categoryDisplayName = 'GNFC';
    } elseif ($category->slug === 'industrial-solvents-commodities' || $category->slug === 'industrial-solvents') {
        $categoryDisplayName = 'Industrial Solvents';
    }

    $isSolventGroup = ($category->slug === 'industrial-solvents-commodities' || $category->slug === 'industrial-solvents');
    $isSolventChild = $category->parent && ($category->parent->slug === 'industrial-solvents-commodities' || $category->parent->slug === 'industrial-solvents');

    // Force showing direct products for Organic Products and GNFC categories (matching user screenshots)
    $showDirectProducts = in_array($category->slug, ['organic-products', 'gnfc-products', 'gnfc']);

    $children = $category->children;
    $products = $category->getAllProductsRecursive();
@endphp

@if($isSolventChild)
    <li>
        <a href="{{ $category->url }}">
            {{ ($category->slug === 'cleaning-degreasing-solvents') ? 'By Products' : $category->name }}
        </a>
    </li>
@elseif($isSolventGroup)
    <li class="has-dropdown">
        <a href="{{ $category->url }}">{{ $categoryDisplayName }}</a>
        <ul class="sub-menu">
            <li>
                <a href="{{ url('/category/paint-coating-solvents') }}">Paint &amp; Coating Industry Solvents</a>
            </li>
            <li>
                <a href="{{ url('/category/pharmaceutical-chemical-solvents') }}">Pharmaceutical &amp; Chemical Solvents</a>
            </li>
            <li>
                <a href="{{ url('/category/cleaning-degreasing-solvents') }}">By Products</a>
            </li>
            <li>
                <a href="{{ route('products.index') }}">Solvent &amp; Chemical Products Catalog</a>
            </li>
        </ul>
    </li>
@elseif($showDirectProducts && $products->count() > 0)
    <li class="has-dropdown">
        <a href="{{ $category->url }}">{{ $categoryDisplayName }}</a>
        <ul class="sub-menu">
            @foreach($products as $product)
                <li>
                    <a href="{{ $product->full_url }}">{{ $product->name }}</a>
                </li>
            @endforeach
        </ul>
    </li>
@elseif($children->count() > 0)
    <li class="has-dropdown">
        <a href="{{ $category->url }}">{{ $categoryDisplayName }}</a>
        <ul class="sub-menu">
            @foreach($children as $child)
                <li>
                    <a href="{{ $child->url }}">{{ $child->name }}</a>
                </li>
            @endforeach
        </ul>
    </li>
@elseif($products->count() > 0)
    <li class="has-dropdown">
        <a href="{{ $category->url }}">{{ $categoryDisplayName }}</a>
        <ul class="sub-menu">
            @foreach($products as $product)
                <li>
                    <a href="{{ $product->full_url }}">{{ $product->name }}</a>
                </li>
            @endforeach
        </ul>
    </li>
@else
    <li>
        <a href="{{ $category->url }}">{{ $categoryDisplayName }}</a>
    </li>
@endif
