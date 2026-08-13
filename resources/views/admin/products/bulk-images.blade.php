@extends('admin.layouts.admin')

@section('title', 'Bulk Product Image Manager — SRCIL Admin ERP')
@section('page_title', 'Bulk Product Image Manager')

@section('content')
<div class="card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary font-bold"><i class="fa-solid fa-cloud-arrow-up me-2"></i> Bulk Product Image Upload & Automatic Mapping</h4>
            <p class="text-muted mb-0">Upload multiple chemical product images at once. The system automatically detects product names from filenames (`nitric-acid.jpg` → `Nitric Acid`).</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Product Catalog
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('upload_details'))
    <div class="alert alert-info py-2 px-3 mb-4">
        <h6 class="font-semibold mb-2"><i class="fa-solid fa-info-circle me-1"></i> Upload Operations Summary:</h6>
        <ul class="mb-0 text-13">
            @foreach(session('upload_details') as $detail)
            <li>{{ $detail }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Audit Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="p-3 bg-light rounded border text-center">
                <div class="fs-4 font-bold text-dark">{{ $audit['total_products'] }}</div>
                <div class="text-muted text-12 font-semibold">Total Products in DB</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-3 bg-light rounded border text-center">
                <div class="fs-4 font-bold text-success">{{ $audit['products_with_images'] }}</div>
                <div class="text-muted text-12 font-semibold">Products With Images</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-3 bg-light rounded border text-center">
                <div class="fs-4 font-bold text-danger">{{ $audit['products_without_images'] }}</div>
                <div class="text-muted text-12 font-semibold">Products Without Images</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-3 bg-light rounded border text-center">
                <div class="fs-4 font-bold text-info">{{ count($candidateImages) }}</div>
                <div class="text-muted text-12 font-semibold">Local Image Candidates</div>
            </div>
        </div>
    </div>

    <!-- Products Without Images Report Accordion -->
    @if(!empty($audit['missing_image_products']))
    <div class="card mb-4 border-warning">
        <div class="card-header bg-warning bg-opacity-10 d-flex justify-content-between align-items-center py-2">
            <h6 class="mb-0 font-bold text-dark"><i class="fa-solid fa-triangle-exclamation text-warning me-2"></i> Products Without Images ({{ count($audit['missing_image_products']) }})</h6>
            <button class="btn btn-sm btn-outline-dark" type="button" data-bs-toggle="collapse" data-bs-target="#missingProductsCollapse">
                Toggle List
            </button>
        </div>
        <div class="collapse show" id="missingProductsCollapse">
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                    <table class="table table-sm table-hover mb-0 text-13">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th>#</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($audit['missing_image_products'] as $idx => $missing)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td class="font-semibold text-dark">{{ $missing['name'] }}</td>
                                <td>{{ $missing['category'] }}</td>
                                <td><span class="badge bg-danger">❌ Missing Image</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Drag & Drop Bulk Upload Box -->
    <form action="{{ route('admin.products.process-bulk-images') }}" method="POST" enctype="multipart/form-data" id="bulkUploadForm">
        @csrf
        <div class="upload-dropzone p-5 text-center border-2 border-dashed rounded-3 bg-light mb-4" id="dropzone">
            <i class="fa-solid fa-cloud-arrow-up text-primary display-4 mb-3"></i>
            <h5 class="font-bold">Drag & Drop Product Images Here</h5>
            <p class="text-muted text-13 mb-3">Upload any number of chemical product images (20, 50, 100, 200+). Multiple files supported: JPG, PNG, WEBP, GIF.</p>
            
            <input type="file" name="images[]" id="fileInput" multiple accept="image/jpeg,image/png,image/webp,image/gif" class="d-none">
            <button type="button" class="btn btn-brand-primary px-4" onclick="document.getElementById('fileInput').click();">
                <i class="fa-solid fa-folder-open me-2"></i> Browse Computer Files
            </button>
        </div>

        <!-- Live Upload Progress Container -->
        <div id="progressContainer" class="d-none mb-4 p-3 bg-light border rounded shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span id="progressTitle" class="font-bold text-dark text-14"><i class="fa-solid fa-spinner fa-spin me-2 text-primary"></i> Processing Bulk Upload...</span>
                <span id="progressPercentText" class="font-bold text-primary text-14">0%</span>
            </div>
            <div class="progress" style="height: 18px;">
                <div id="uploadProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%;">0%</div>
            </div>
            <div id="progressStatusText" class="text-muted text-12 mt-2 text-center">Processing 0 / 0 images...</div>
        </div>

        <!-- Real-Time Match Preview Table -->
        <div id="previewArea" class="d-none mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="font-bold mb-0"><i class="fa-solid fa-list-check me-2"></i> Upload Preview & Product Match Detection (<span id="totalFileCountBadge">0</span> Files)</h5>
                <div class="text-muted text-12 font-semibold" id="batchInfoText">Batch Chunking Active — No 20 Image Limit</div>
            </div>

            <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th style="width: 70px;">Preview</th>
                            <th>Uploaded File</th>
                            <th>Detected Match Status</th>
                            <th>Existing Image Option</th>
                            <th>Assign to Product</th>
                        </tr>
                    </thead>
                    <tbody id="previewTableBody">
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-3">
                <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">Clear Selection</button>
                <button type="submit" id="submitBtn" class="btn btn-brand-green px-4 font-bold">
                    <i class="fa-solid fa-check-double me-1"></i> Upload & Apply All Matched Product Images
                </button>
            </div>
        </div>
    </form>
</div>

@push('styles')
<style>
    .upload-dropzone {
        border-color: #CBD5E1 !important;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .upload-dropzone:hover, .upload-dropzone.dragover {
        border-color: var(--brand-blue) !important;
        background-color: #EFF6FF !important;
    }
    .border-dashed { border-style: dashed !important; }
</style>
@endpush

@push('scripts')
<script>
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('fileInput');
    const previewArea = document.getElementById('previewArea');
    const previewTableBody = document.getElementById('previewTableBody');
    const progressContainer = document.getElementById('progressContainer');
    const uploadProgressBar = document.getElementById('uploadProgressBar');
    const progressTitle = document.getElementById('progressTitle');
    const progressPercentText = document.getElementById('progressPercentText');
    const progressStatusText = document.getElementById('progressStatusText');
    const totalFileCountBadge = document.getElementById('totalFileCountBadge');
    const bulkUploadForm = document.getElementById('bulkUploadForm');
    const submitBtn = document.getElementById('submitBtn');

    const productsList = @json($products);
    const BATCH_SIZE = 25; // Sequential HTTP request batch size

    let selectedFiles = [];
    let previewItems = [];

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, () => dropzone.classList.add('dragover'), false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, () => dropzone.classList.remove('dragover'), false);
    });

    dropzone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        selectedFiles = Array.from(dt.files);
        handleFiles(selectedFiles);
    });

    fileInput.addEventListener('change', function() {
        selectedFiles = Array.from(this.files);
        handleFiles(selectedFiles);
    });

    function updateProgress(title, percent, status) {
        progressContainer.classList.remove('d-none');
        if (title) progressTitle.innerHTML = title;
        if (percent !== null && percent !== undefined) {
            uploadProgressBar.style.width = percent + '%';
            uploadProgressBar.innerText = percent + '%';
            progressPercentText.innerText = percent + '%';
        }
        if (status) progressStatusText.innerText = status;
    }

    async function handleFiles(files) {
        if (!files || files.length === 0) return;

        previewTableBody.innerHTML = '';
        previewItems = [];
        previewArea.classList.remove('d-none');
        totalFileCountBadge.innerText = files.length;

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const totalFiles = files.length;

        updateProgress('<i class="fa-solid fa-spinner fa-spin me-2 text-primary"></i> Analyzing Filenames & Product Matches...', 0, `Processing 0 / ${totalFiles} images...`);

        for (let i = 0; i < totalFiles; i += BATCH_SIZE) {
            const batchFiles = files.slice(i, i + BATCH_SIZE);
            const formData = new FormData();

            batchFiles.forEach(file => {
                formData.append('images[]', file);
            });

            try {
                const res = await fetch("{{ route('admin.products.preview-bulk-images') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (!res.ok) {
                    if (res.status === 413 || res.status === 500) {
                        alert("Upload exceeded the server's configured upload limit. Please increase PHP/server upload settings.");
                    }
                    throw new Error(`HTTP Error ${res.status}`);
                }

                const data = await res.json();
                if (data && data.status === 'success') {
                    data.items.forEach((item, batchIdx) => {
                        const globalIdx = i + batchIdx;
                        const file = batchFiles[batchIdx];

                        renderPreviewRow(item, globalIdx, file);
                    });
                }
            } catch (e) {
                console.error('Batch preview error:', e);
            }

            const currentProcessed = Math.min(i + BATCH_SIZE, totalFiles);
            const percent = Math.round((currentProcessed / totalFiles) * 100);
            updateProgress('<i class="fa-solid fa-spinner fa-spin me-2 text-primary"></i> Analyzing Filenames & Product Matches...', percent, `Analyzed ${currentProcessed} / ${totalFiles} images...`);
        }

        setTimeout(() => {
            progressContainer.classList.add('d-none');
        }, 800);
    }

    function renderPreviewRow(item, idx, file) {
        const tr = document.createElement('tr');

        let badgeClass = 'bg-secondary';
        if (['exact', 'exact_chemical', 'exact_slug', 'cas_match', 'hsn_match'].includes(item.match_type)) badgeClass = 'bg-success';
        else if (item.match_type === 'normalized') badgeClass = 'bg-primary';
        else if (item.match_type === 'ambiguous') badgeClass = 'bg-warning text-dark';
        else if (item.match_type === 'none') badgeClass = 'bg-danger';

        // Product options dropdown
        let productOptionsHtml = `<option value="auto" ${!item.product_id ? 'selected' : ''}>-- Choose / Auto Match --</option>`;
        
        if (item.match_type === 'ambiguous' && item.candidates && item.candidates.length > 0) {
            productOptionsHtml += `<optgroup label="⚠️ Ambiguous Candidates (Please Select)">`;
            item.candidates.forEach(cand => {
                productOptionsHtml += `<option value="${cand.id}">👉 ${cand.name}</option>`;
            });
            productOptionsHtml += `</optgroup>`;
        }

        productOptionsHtml += `<optgroup label="All Products">`;
        productsList.forEach(p => {
            const selected = (item.product_id === p.id) ? 'selected' : '';
            productOptionsHtml += `<option value="${p.id}" ${selected}>${p.name}</option>`;
        });
        productOptionsHtml += `</optgroup>`;

        const imgUrl = URL.createObjectURL(file);

        let existingImageHtml = `<span class="text-muted text-12">New Image</span>`;
        if (item.has_existing_image) {
            existingImageHtml = `
                <div class="text-12 font-semibold text-warning">
                    <i class="fa-solid fa-triangle-exclamation"></i> Product has existing image
                </div>
                <select name="replace_modes[${idx}]" class="form-select form-select-sm mt-1 text-12">
                    <option value="replace" selected>Replace Existing</option>
                    <option value="skip">Skip Product</option>
                </select>
            `;
        }

        tr.innerHTML = `
            <td><img src="${imgUrl}" style="height:50px; width:50px; object-fit:cover;" class="rounded border"></td>
            <td>
                <div class="font-semibold text-13">${item.original_name}</div>
                <div class="text-muted text-11">${(file.size / 1024).toFixed(1)} KB</div>
            </td>
            <td>
                <span class="badge ${badgeClass} px-2 py-1">${item.label}</span>
                ${item.product_name ? `<div class="text-12 font-semibold text-dark mt-1">Target: ${item.product_name}</div>` : ''}
            </td>
            <td>
                ${existingImageHtml}
            </td>
            <td>
                <select name="product_ids[${idx}]" id="product_select_${idx}" class="form-select form-select-sm">
                    ${productOptionsHtml}
                </select>
            </td>
        `;

        previewTableBody.appendChild(tr);
    }

    // Handle AJAX Form Submission in Chunks
    bulkUploadForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        if (!selectedFiles || selectedFiles.length === 0) return;

        submitBtn.disabled = true;
        const totalFiles = selectedFiles.length;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        let totalAssigned = 0;
        let totalReplaced = 0;
        let totalSkipped = 0;
        let totalUnmatched = 0;
        let cumulativeDetails = [];

        updateProgress('<i class="fa-solid fa-cloud-arrow-up fa-bounce me-2 text-success"></i> Uploading & Saving Images in Batches...', 0, `Processing 0 / ${totalFiles} images...`);

        for (let i = 0; i < totalFiles; i += BATCH_SIZE) {
            const batchFiles = selectedFiles.slice(i, i + BATCH_SIZE);
            const formData = new FormData();

            batchFiles.forEach((file, batchIdx) => {
                const globalIdx = i + batchIdx;
                formData.append('images[]', file);

                const selectElem = document.getElementById(`product_select_${globalIdx}`);
                formData.append(`product_ids[${batchIdx}]`, selectElem ? selectElem.value : 'auto');

                const replaceSelect = document.querySelector(`select[name="replace_modes[${globalIdx}]"]`);
                formData.append(`replace_modes[${batchIdx}]`, replaceSelect ? replaceSelect.value : 'replace');
            });

            try {
                const res = await fetch("{{ route('admin.products.process-bulk-images') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                let data = null;
                try {
                    data = await res.json();
                } catch (jsonErr) {}

                if (!res.ok) {
                    let errorMsg = 'Upload Error: Request failed.';
                    if (res.status === 422) {
                        errorMsg = (data && data.message) ? data.message : 'Validation Error: Please check uploaded image files.';
                    } else if (res.status === 419) {
                        errorMsg = 'Session Expired (CSRF mismatch). Please refresh the page and try again.';
                    } else if (res.status === 413) {
                        errorMsg = 'Upload rejected: Server post_max_size / upload_max_filesize limit exceeded.';
                    } else if (res.status === 500) {
                        errorMsg = (data && data.message) ? data.message : 'Server Exception during upload. Please check laravel.log.';
                    } else if (data && data.message) {
                        errorMsg = data.message;
                    }
                    alert(errorMsg);
                    submitBtn.disabled = false;
                    return;
                }

                if (data && data.status === 'success') {
                    totalAssigned += data.assigned_count || 0;
                    totalReplaced += data.replaced_count || 0;
                    totalSkipped += data.skipped_count || 0;
                    totalUnmatched += data.unmatched_count || 0;
                    if (data.details) {
                        cumulativeDetails.push(...data.details);
                    }
                }
            } catch (e) {
                console.error('Upload batch execution error:', e);
                alert("Upload failed: " + e.message);
                submitBtn.disabled = false;
                return;
            }

            const currentProcessed = Math.min(i + BATCH_SIZE, totalFiles);
            const percent = Math.round((currentProcessed / totalFiles) * 100);
            updateProgress('<i class="fa-solid fa-cloud-arrow-up fa-bounce me-2 text-success"></i> Uploading & Saving Images in Batches...', percent, `Processing ${currentProcessed} / ${totalFiles} images...`);
        }

        updateProgress('<i class="fa-solid fa-circle-check me-2 text-success"></i> Upload Completed Successfully!', 100, `Completed processing ${totalFiles} / ${totalFiles} images.`);

        setTimeout(() => {
            window.location.reload();
        }, 1000);
    });

    function resetForm() {
        fileInput.value = '';
        selectedFiles = [];
        previewTableBody.innerHTML = '';
        previewArea.classList.add('d-none');
        progressContainer.classList.add('d-none');
        submitBtn.disabled = false;
    }
</script>
@endpush
@endsection
