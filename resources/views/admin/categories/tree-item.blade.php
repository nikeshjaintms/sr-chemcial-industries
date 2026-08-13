@php
    $showDirectProductsInAdminTree = in_array($category->slug, ['organic-products', 'gnfc-products', 'gnfc']);
@endphp

<li class="list-group-item border-0 ps-3 py-2">
    <div class="d-flex align-items-center justify-content-between bg-light p-2 rounded border-start border-3 {{ $category->parent_id ? 'border-info' : 'border-primary' }}">
        <div class="d-flex align-items-center gap-2">
            <i class="fa-solid {{ ($category->allChildren->count() > 0 && !$showDirectProductsInAdminTree) ? 'fa-folder-open text-warning' : 'fa-folder text-primary' }}"></i>
            <span class="font-bold text-dark">{{ $category->name }}</span>
            <code class="text-12 text-muted">({{ $category->slug }})</code>
            @if(!$category->status)
                <span class="badge bg-danger-subtle text-danger text-11">Disabled</span>
            @endif
            <span class="badge bg-secondary-subtle text-dark text-11 ms-2">
                {{ $category->getAllProductsRecursive()->count() }} Products
            </span>
        </div>
        <div class="d-flex align-items-center gap-1">
            <a href="{{ route('admin.categories.create', ['parent_id' => $category->id]) }}" class="btn btn-xs btn-outline-success py-0 px-2 text-12" title="Add Child Category under {{ $category->name }}">
                <i class="fa-solid fa-plus"></i> Add Child
            </a>
            <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-xs btn-outline-primary py-0 px-2 text-12" title="Edit Category">
                <i class="fa-solid fa-pen"></i> Edit
            </a>
            <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this category and its child categories/products linkage?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-xs btn-outline-danger py-0 px-2 text-12" title="Delete">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </form>
        </div>
    </div>

    @if($category->allChildren->count() === 0 || $showDirectProductsInAdminTree)
        @php
            $subProducts = $category->getAllProductsRecursive();
        @endphp
        @if($subProducts->count() > 0)
            <div class="ps-4 pt-1 pb-1">
                <div class="d-flex flex-wrap gap-1 align-items-center">
                    <span class="text-11 text-muted font-semibold me-1"><i class="fa-solid fa-flask text-primary"></i> Direct Category Products ({{ $subProducts->count() }}):</span>
                    @foreach($subProducts as $p)
                        <span class="badge bg-white text-dark border text-11 py-1 px-2 me-1 mb-1 shadow-sm">
                            <i class="fa-solid fa-box-open text-primary me-1"></i>{{ $p->name }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif
    @endif

    @if($category->allChildren->count() > 0 && !$showDirectProductsInAdminTree)
        <ul class="list-group list-group-flush ms-3 border-start ps-2">
            @foreach($category->allChildren as $child)
                @include('admin.categories.tree-item', ['category' => $child])
            @endforeach
        </ul>
    @endif
</li>
