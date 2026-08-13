@extends('admin.layouts.admin')

@section('title', 'Bulk Product Image Manager — SRCIL Admin ERP')
@section('page_title', 'Bulk Product Image Manager')

@section('content')
<div class="card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary font-bold"><i class="fa-solid fa-cloud-arrow-up me-2"></i> Bulk Product Image Auto-Matching & Assignment</h4>
            <p class="text-muted mb-0">Upload chemical product images. The system automatically reads original filenames (<code>nitric-acid.jpg</code> → <code>Nitric Acid</code>), assigns them to products, and deletes unmatched/ambiguous files.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-danger font-semibold d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#deleteAllImagesModalBulk">
                <i class="fa-solid fa-trash-can"></i> Delete All Product Images
            </button>
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Product Catalog
            </a>
        </div>
    </div>

    <!-- Modal: Delete All Product Images Confirmation -->
    <div class="modal fade" id="deleteAllImagesModalBulk" tabindex="-1" aria-labelledby="deleteAllImagesModalBulkLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-bold" id="deleteAllImagesModalBulkLabel">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i> Delete All Product Images
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-warning border-start border-4 border-warning mb-3">
                        <p class="font-bold text-dark mb-1"><i class="fa-solid fa-triangle-exclamation text-warning me-1"></i> Strong Confirmation Required</p>
                        <p class="text-13 text-dark mb-0">
                            Are you sure you want to remove all product images?<br>
                            This will remove image assignments and delete the corresponding stored image files.<br>
                            <strong>Products themselves will NOT be deleted.</strong>
                        </p>
                    </div>
                    <ul class="text-13 text-muted mb-0">
                        <li><i class="fa-solid fa-check text-success me-1"></i> Database product records remain 100% intact.</li>
                        <li><i class="fa-solid fa-check text-success me-1"></i> Categories, names, and descriptions are NOT modified.</li>
                        <li><i class="fa-solid fa-check text-success me-1"></i> MSDS and Specification PDF files are NOT deleted.</li>
                    </ul>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary font-semibold" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('admin.products.delete-all-images') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger font-bold">
                            <i class="fa-solid fa-trash-can me-1"></i> Yes, Remove All Product Images
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('upload_details'))
    <div class="alert alert-info py-3 px-4 mb-4 border-0 shadow-sm rounded-3">
        <h6 class="font-bold text-dark mb-2"><i class="fa-solid fa-list-check me-2 text-primary"></i> Bulk Processing Log Summary:</h6>
        <ul class="mb-0 text-13 font-semibold">
            @foreach(session('upload_details') as $detail)
            <li class="mb-1">{{ $detail }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Audit KPI Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="p-3 bg-light rounded border text-center border-start border-4 border-primary">
                <div class="fs-4 font-bold text-dark">{{ $audit['total'] }}</div>
                <div class="text-muted text-12 font-semibold">Total Products in DB</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 bg-light rounded border text-center border-start border-4 border-success">
                <div class="fs-4 font-bold text-success">{{ $audit['assigned'] }}</div>
                <div class="text-muted text-12 font-semibold">Products With Images</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 bg-light rounded border text-center border-start border-4 border-danger">
                <div class="fs-4 font-bold text-danger">{{ $audit['without_images_count'] }}</div>
                <div class="text-muted text-12 font-semibold">Products Without Images</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 bg-light rounded border text-center border-start border-4 border-info">
                <div class="fs-4 font-bold text-info">{{ count($candidateImages) }}</div>
                <div class="text-muted text-12 font-semibold">Local Image Files</div>
            </div>
        </div>
    </div>

    <!-- Products Without Images Report -->
    @if(!empty($audit['without_images']))
    <div class="card mb-4 border-danger">
        <div class="card-header bg-danger bg-opacity-10 d-flex justify-content-between align-items-center py-2">
            <h6 class="mb-0 font-bold text-dark"><i class="fa-solid fa-triangle-exclamation text-danger me-2"></i> Products Without Images ({{ count($audit['without_images']) }})</h6>
            <button class="btn btn-sm btn-outline-danger font-semibold text-12" type="button" data-bs-toggle="collapse" data-bs-target="#missingProductsCollapse">
                Toggle List
            </button>
        </div>
        <div class="collapse show" id="missingProductsCollapse">
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                    <table class="table table-sm table-hover mb-0 text-13 align-middle">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th style="width: 45%;">Product Name</th>
                                <th style="width: 25%;">Category</th>
                                <th style="width: 25%;">Image Display State</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($audit['without_images'] as $idx => $missing)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td class="font-semibold text-dark">{{ $missing->name }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $missing->category ? $missing->category->name : 'General' }}</span></td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary border px-2 py-1 font-semibold">
                                        <i class="fa-solid fa-image me-1"></i> Image Not Available (Placeholder Active)
                                    </span>
                                </td>
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

        <!-- Mode Option -->
        <div class="mb-4 bg-light p-3 rounded border">
            <label class="form-label font-bold text-dark text-14 mb-2">
                <i class="fa-solid fa-shield-halved text-primary me-1"></i> Existing Product Image Handling
            </label>
            <div class="d-flex gap-4">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="mode" id="global_mode_skip" value="skip" checked>
                    <label class="form-check-label font-semibold text-dark cursor-pointer" for="global_mode_skip">
                        <i class="fa-solid fa-forward-step text-success me-1"></i> Skip (Default) — Do not overwrite products that already have an image
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="mode" id="global_mode_replace" value="replace">
                    <label class="form-check-label font-semibold text-dark cursor-pointer" for="global_mode_replace">
                        <i class="fa-solid fa-rotate text-warning me-1"></i> Replace — Overwrite existing image and safely delete old image file
                    </label>
                </div>
            </div>
        </div>

        <div class="upload-dropzone p-5 text-center border-2 border-dashed rounded-3 bg-light mb-4" id="dropzone">
            <i class="fa-solid fa-cloud-arrow-up text-primary display-4 mb-3"></i>
            <h5 class="font-bold text-dark mb-1">Drag & Drop Product Images Here</h5>
            <p class="text-muted text-13 mb-3">
                Upload image files named after products (e.g. <code>Nitric Acid.jpg</code>, <code>Caustic-Soda-Flakes.png</code>, <code>Sodium_Hypochlorite.webp</code>).<br>
                <span class="text-danger font-semibold"><i class="fa-solid fa-trash me-1"></i> Unmatched or Ambiguous image files will be automatically discarded/deleted without saving.</span>
            </p>
            
            <input type="file" name="images[]" id="fileInput" multiple accept="image/jpeg,image/png,image/webp,image/gif" class="d-none">
            <button type="button" class="btn btn-brand-primary px-4 py-2 font-semibold" onclick="document.getElementById('fileInput').click();">
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
                <h5 class="font-bold mb-0 text-dark"><i class="fa-solid fa-list-check me-2 text-primary"></i> Image Match Detection Preview (<span id="totalFileCountBadge">0</span> Files)</h5>
                <div class="text-muted text-12 font-semibold">Automatic Matching Active</div>
            </div>

            <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
                <table class="table table-custom align-middle mb-0">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th style="width: 60px;">Preview</th>
                            <th>Original Filename</th>
                            <th>Matched Product</th>
                            <th>Status</th>
                            <th>Target Assignment</th>
                        </tr>
                    </thead>
                    <tbody id="previewTableBody">
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-3">
                <button type="button" class="btn btn-outline-secondary px-4 py-2 font-semibold" onclick="resetForm()">Clear Selection</button>
                <button type="submit" id="submitBtn" class="btn btn-brand-green px-4 py-2 font-bold">
                    <i class="fa-solid fa-upload me-1"></i> Upload & Auto Assign Product Images
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
    const BATCH_SIZE = 25;

    let selectedFiles = [];

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
        previewArea.classList.remove('d-none');
        totalFileCountBadge.innerText = files.length;

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const totalFiles = files.length;

        updateProgress('<i class="fa-solid fa-spinner fa-spin me-2 text-primary"></i> Analyzing Original Filenames & Product Matches...', 0, `Processing 0 / ${totalFiles} images...`);

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
            updateProgress('<i class="fa-solid fa-spinner fa-spin me-2 text-primary"></i> Analyzing Original Filenames & Product Matches...', percent, `Analyzed ${currentProcessed} / ${totalFiles} images...`);
        }

        setTimeout(() => {
            progressContainer.classList.add('d-none');
        }, 800);
    }

    function renderPreviewRow(item, idx, file) {
        const tr = document.createElement('tr');

        let badgeClass = 'bg-secondary';
        let statusLabel = item.label || 'Not Found';

        if (item.status === 'MATCHED' || ['exact', 'exact_name', 'exact_chemical', 'exact_slug', 'unique_candidate'].includes(item.match_type)) {
            badgeClass = 'bg-success';
            statusLabel = '✅ MATCHED';
        } else if (item.status === 'ALREADY EXISTS') {
            badgeClass = 'bg-info text-dark';
            statusLabel = '⏭️ ALREADY EXISTS';
        } else if (item.status === 'AMBIGUOUS' || item.match_type === 'ambiguous') {
            badgeClass = 'bg-warning text-dark';
            statusLabel = '⚠️ AMBIGUOUS (Will be deleted)';
        } else {
            badgeClass = 'bg-secondary';
            statusLabel = '❌ NOT FOUND (Will be deleted)';
        }

        // Product options dropdown
        let productOptionsHtml = `<option value="auto" ${!item.product_id ? 'selected' : ''}>-- Auto Matched / Choose --</option>`;
        
        if (item.candidates && item.candidates.length > 0) {
            productOptionsHtml += `<optgroup label="⚠️ Candidates (Ambiguous)">`;
            item.candidates.forEach(candName => {
                const candObj = productsList.find(p => p.name === candName);
                if (candObj) {
                    productOptionsHtml += `<option value="${candObj.id}">👉 ${candObj.name}</option>`;
                }
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

        tr.innerHTML = `
            <td><img src="${imgUrl}" style="height:48px; width:48px; object-fit:cover;" class="rounded border"></td>
            <td>
                <div class="font-semibold text-13 text-dark"><i class="fa-solid fa-file-image text-primary me-1"></i> ${escapeHtml(item.original_name)}</div>
                <div class="text-muted text-11">${(file.size / 1024).toFixed(1)} KB</div>
            </td>
            <td class="font-semibold text-primary">
                ${item.product_name ? escapeHtml(item.product_name) : '—'}
            </td>
            <td>
                <span class="badge ${badgeClass} px-2 py-1">${escapeHtml(statusLabel)}</span>
                <div class="text-11 text-muted mt-1">${escapeHtml(item.message || '')}</div>
            </td>
            <td>
                <select name="product_ids[${idx}]" id="product_select_${idx}" class="form-select form-select-sm">
                    ${productOptionsHtml}
                </select>
            </td>
        `;

        previewTableBody.appendChild(tr);
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // Handle AJAX Form Submission
    bulkUploadForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        if (!selectedFiles || selectedFiles.length === 0) return;

        submitBtn.disabled = true;
        const totalFiles = selectedFiles.length;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const selectedMode = document.querySelector('input[name="mode"]:checked').value;

        let totalAssigned = 0;
        let totalReplaced = 0;
        let totalSkipped = 0;
        let totalDeletedUnmatched = 0;
        let cumulativeDetails = [];

        updateProgress('<i class="fa-solid fa-cloud-arrow-up fa-bounce me-2 text-success"></i> Uploading, Assigning & Cleaning Images in Batches...', 0, `Processing 0 / ${totalFiles} images...`);

        for (let i = 0; i < totalFiles; i += BATCH_SIZE) {
            const batchFiles = selectedFiles.slice(i, i + BATCH_SIZE);
            const formData = new FormData();

            formData.append('mode', selectedMode);

            batchFiles.forEach((file, batchIdx) => {
                const globalIdx = i + batchIdx;
                formData.append('images[]', file);

                const selectElem = document.getElementById(`product_select_${globalIdx}`);
                formData.append(`product_ids[${batchIdx}]`, selectElem ? selectElem.value : 'auto');
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
                    if (data && data.message) {
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
                    totalDeletedUnmatched += data.deleted_unmatched_count || 0;
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
            updateProgress('<i class="fa-solid fa-cloud-arrow-up fa-bounce me-2 text-success"></i> Uploading & Processing Images in Batches...', percent, `Processing ${currentProcessed} / ${totalFiles} images...`);
        }

        updateProgress('<i class="fa-solid fa-circle-check me-2 text-success"></i> Bulk Upload & Processing Completed!', 100, `Completed processing ${totalFiles} images.`);

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
