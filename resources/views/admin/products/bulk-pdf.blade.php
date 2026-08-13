@extends('admin.layouts.admin')

@section('title', 'Bulk MSDS & Specification PDF Auto-Matching — Admin Portal')
@section('page_title', 'Bulk PDF Auto-Matching')

@section('content')
<div class="container-fluid p-0">

    <!-- Header Instruction Banner -->
    <div class="card-custom p-4 mb-4 bg-white border-start border-4 border-primary">
        <div class="d-flex align-items-start gap-3">
            <div class="bg-primary-subtle text-primary p-3 rounded-circle d-none d-md-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                <i class="fa-solid fa-file-pdf fs-4"></i>
            </div>
            <div>
                <h2 class="h5 font-bold text-dark mb-1">Bulk MSDS / Specification PDF Auto-Matching</h2>
                <p class="text-muted text-14 mb-2">
                    Upload multiple PDF files at once. The system will automatically detect and match each PDF to the correct product based on the PDF filename and attach it directly.
                </p>
                <div class="bg-light p-2 rounded text-13 text-secondary border">
                    <i class="fa-solid fa-lightbulb text-warning me-1"></i>
                    <strong>Instruction:</strong> Upload PDF files using the product name as the filename (e.g. <code>Nitric Acid.pdf</code>, <code>Caustic Soda Flakes.pdf</code>, <code>Hydrochloric-Acid.pdf</code>, <code>Sodium_Hypochlorite.pdf</code>).
                </div>
            </div>
        </div>
    </div>

    <!-- Main Upload Card -->
    <div class="card-custom p-4 mb-4">
        <form id="bulkPdfForm" action="{{ route('admin.products.process-bulk-pdf') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row g-4 mb-4">
                <!-- PDF Type Selector -->
                <div class="col-md-6">
                    <label class="form-label font-semibold text-dark text-14">
                        <i class="fa-solid fa-layer-group me-1 text-primary"></i> 1. Select PDF Attachment Type <span class="text-danger">*</span>
                    </label>
                    <div class="d-flex gap-3">
                        <div class="form-check custom-radio-card p-3 rounded border w-50 bg-light">
                            <input class="form-check-input me-2" type="radio" name="pdf_type" id="pdf_type_msds" value="msds" checked>
                            <label class="form-check-label font-semibold text-dark cursor-pointer" for="pdf_type_msds">
                                <i class="fa-solid fa-flask text-danger me-1"></i> MSDS File
                                <div class="text-12 text-muted font-normal mt-1">Attaches to Product MSDS document field</div>
                            </label>
                        </div>
                        <div class="form-check custom-radio-card p-3 rounded border w-50 bg-light">
                            <input class="form-check-input me-2" type="radio" name="pdf_type" id="pdf_type_spec" value="specification">
                            <label class="form-check-label font-semibold text-dark cursor-pointer" for="pdf_type_spec">
                                <i class="fa-solid fa-file-contract text-primary me-1"></i> Specification File
                                <div class="text-12 text-muted font-normal mt-1">Attaches to Product Technical Specification field</div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Existing File Handling -->
                <div class="col-md-6">
                    <label class="form-label font-semibold text-dark text-14">
                        <i class="fa-solid fa-shield-halved me-1 text-primary"></i> 2. Existing PDF Handling <span class="text-danger">*</span>
                    </label>
                    <div class="d-flex gap-3">
                        <div class="form-check custom-radio-card p-3 rounded border w-50 bg-light">
                            <input class="form-check-input me-2" type="radio" name="existing_mode" id="mode_skip" value="skip" checked>
                            <label class="form-check-label font-semibold text-dark cursor-pointer" for="mode_skip">
                                <i class="fa-solid fa-forward-step text-success me-1"></i> Skip (Safe Default)
                                <div class="text-12 text-muted font-normal mt-1">Do not overwrite existing PDFs if already attached</div>
                            </label>
                        </div>
                        <div class="form-check custom-radio-card p-3 rounded border w-50 bg-light">
                            <input class="form-check-input me-2" type="radio" name="existing_mode" id="mode_replace" value="replace">
                            <label class="form-check-label font-semibold text-dark cursor-pointer" for="mode_replace">
                                <i class="fa-solid fa-rotate text-warning me-1"></i> Replace / Overwrite
                                <div class="text-12 text-muted font-normal mt-1">Overwrite existing PDF attachment for matched product</div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PDF Multi-File Input -->
            <div class="mb-4">
                <label class="form-label font-semibold text-dark text-14">
                    <i class="fa-solid fa-cloud-arrow-up me-1 text-primary"></i> 3. Select PDF Files (Multiple Files Allowed) <span class="text-danger">*</span>
                </label>
                <div class="border border-2 border-dashed rounded-3 p-4 text-center bg-light" id="dropzoneWrap">
                    <i class="fa-solid fa-file-pdf text-danger mb-2" style="font-size: 42px;"></i>
                    <h4 class="h6 font-semibold text-dark mb-1">Drag & Drop PDF Files Here or Click to Browse</h4>
                    <p class="text-13 text-muted mb-3">Only PDF files (<code>.pdf</code>) are allowed. Maximum 20MB per file.</p>
                    
                    <input type="file" name="pdf_files[]" id="pdfFileInput" class="d-none" multiple accept="application/pdf,.pdf">
                    <button type="button" class="btn btn-outline-primary btn-sm px-4 rounded-pill font-semibold" onclick="$('#pdfFileInput').click();">
                        <i class="fa-solid fa-folder-open me-1"></i> Browse PDF Files
                    </button>

                    <div id="selectedFilesSummary" class="mt-3 text-13 font-semibold text-primary d-none"></div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex flex-wrap gap-2 justify-content-end">
                <button type="button" id="btnPreviewMatches" class="btn btn-outline-secondary px-4 py-2 font-semibold text-14">
                    <i class="fa-solid fa-eye me-1"></i> Preview Matching
                </button>
                <button type="submit" id="btnConfirmUpload" class="btn btn-brand-primary px-4 py-2 font-semibold text-14">
                    <i class="fa-solid fa-upload me-1"></i> Confirm & Attach PDFs
                </button>
            </div>
        </form>
    </div>

    <!-- Results Section (KPI Summary + Interactive Table) -->
    <div id="resultsWrapper" class="{{ session('summary') ? '' : 'd-none' }}">
        
        <!-- Summary Cards -->
        <div class="row g-3 mb-4" id="summaryCardsRow">
            @php $sum = session('summary', ['total'=>0, 'uploaded'=>0, 'already_exists'=>0, 'not_found'=>0, 'ambiguous'=>0, 'failed'=>0]); @endphp
            <div class="col-6 col-md-2">
                <div class="card-custom p-3 text-center border-start border-4 border-primary">
                    <div class="text-muted text-12 font-semibold uppercase">Total Files</div>
                    <div class="h4 font-bold text-dark mb-0 mt-1" id="kpiTotal">{{ $sum['total'] }}</div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="card-custom p-3 text-center border-start border-4 border-success">
                    <div class="text-muted text-12 font-semibold uppercase">Uploaded</div>
                    <div class="h4 font-bold text-success mb-0 mt-1" id="kpiUploaded">{{ $sum['uploaded'] ?? $sum['matched'] ?? 0 }}</div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="card-custom p-3 text-center border-start border-4 border-info">
                    <div class="text-muted text-12 font-semibold uppercase">Already Exists</div>
                    <div class="h4 font-bold text-info mb-0 mt-1" id="kpiExists">{{ $sum['already_exists'] }}</div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="card-custom p-3 text-center border-start border-4 border-secondary">
                    <div class="text-muted text-12 font-semibold uppercase">Not Found</div>
                    <div class="h4 font-bold text-secondary mb-0 mt-1" id="kpiNotFound">{{ $sum['not_found'] }}</div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="card-custom p-3 text-center border-start border-4 border-warning">
                    <div class="text-muted text-12 font-semibold uppercase">Ambiguous</div>
                    <div class="h4 font-bold text-warning mb-0 mt-1" id="kpiAmbiguous">{{ $sum['ambiguous'] }}</div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="card-custom p-3 text-center border-start border-4 border-danger">
                    <div class="text-muted text-12 font-semibold uppercase">Failed</div>
                    <div class="h4 font-bold text-danger mb-0 mt-1" id="kpiFailed">{{ $sum['failed'] }}</div>
                </div>
            </div>
        </div>

        <!-- Result Table -->
        <div class="card-custom overflow-hidden">
            <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                <h3 class="h6 font-bold text-dark mb-0">
                    <i class="fa-solid fa-list-check text-primary me-2"></i> Auto-Matching Result Table
                </h3>
                <span class="badge bg-light text-dark border font-normal text-12" id="resultTableMode">
                    Mode: Output Results
                </span>
            </div>
            <div class="table-responsive">
                <table class="table table-custom mb-0" id="resultsTable">
                    <thead>
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th style="width: 25%;">File Name</th>
                            <th style="width: 12%;">PDF Type</th>
                            <th style="width: 23%;">Matched Product</th>
                            <th style="width: 15%;">Status</th>
                            <th style="width: 20%;">Message / Details</th>
                        </tr>
                    </thead>
                    <tbody id="resultsTableBody">
                        @if(session('results'))
                            @foreach(session('results') as $index => $row)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="font-semibold text-dark">
                                    <i class="fa-solid fa-file-pdf text-danger me-1"></i> {{ $row['filename'] }}
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ $row['pdf_type'] }}</span></td>
                                <td class="font-semibold text-primary">
                                    {{ $row['matched_product'] !== '-' ? $row['matched_product'] : '—' }}
                                </td>
                                <td>
                                    <span class="badge {{ $row['badge_class'] }} px-2 py-1">{{ $row['status'] }}</span>
                                </td>
                                <td class="text-13 text-muted">{{ $row['message'] }}</td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    
    // File input change listener
    $('#pdfFileInput').on('change', function() {
        const files = this.files;
        if (files.length > 0) {
            $('#selectedFilesSummary')
                .removeClass('d-none')
                .html('<i class="fa-solid fa-circle-check text-success me-1"></i> Selected <strong>' + files.length + '</strong> PDF file(s). Click <em>Preview Matching</em> to analyze before attaching.');
        } else {
            $('#selectedFilesSummary').addClass('d-none').html('');
        }
    });

    // Drag and drop setup
    const dropzone = document.getElementById('dropzoneWrap');
    if (dropzone) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults (e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            dropzone.classList.add('bg-white', 'border-primary');
        }

        function unhighlight(e) {
            dropzone.classList.remove('bg-white', 'border-primary');
        }

        dropzone.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            const fileInput = document.getElementById('pdfFileInput');
            fileInput.files = files;
            $('#pdfFileInput').trigger('change');
        }
    }

    // Preview Matching AJAX Handler
    $('#btnPreviewMatches').on('click', function(e) {
        e.preventDefault();
        
        const files = $('#pdfFileInput')[0].files;
        if (files.length === 0) {
            alert('Please select at least one PDF file first.');
            return;
        }

        const formData = new FormData($('#bulkPdfForm')[0]);
        
        const $btn = $(this);
        const origText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i> Analyzing Filenames...');

        $.ajax({
            url: "{{ route('admin.products.preview-bulk-pdf') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                $btn.prop('disabled', false).html(origText);
                
                if (res.success) {
                    renderResults(res.summary, res.items, 'Preview Mode (Dry Run)');
                } else {
                    alert(res.message || 'Preview analysis failed.');
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html(origText);
                let err = 'Preview request failed.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    err = xhr.responseJSON.message;
                }
                alert(err);
            }
        });
    });

    // Helper function to render table and KPIs dynamically
    function renderResults(summary, items, modeText) {
        $('#kpiTotal').text(summary.total || 0);
        $('#kpiUploaded').text(summary.uploaded !== undefined ? summary.uploaded : (summary.matched || 0));
        $('#kpiExists').text(summary.already_exists || 0);
        $('#kpiNotFound').text(summary.not_found || 0);
        $('#kpiAmbiguous').text(summary.ambiguous || 0);
        $('#kpiFailed').text(summary.failed || 0);

        $('#resultTableMode').text('Mode: ' + modeText);

        let html = '';
        if (items && items.length > 0) {
            items.forEach(function(row, idx) {
                html += '<tr>';
                html += '<td>' + (idx + 1) + '</td>';
                html += '<td class="font-semibold text-dark"><i class="fa-solid fa-file-pdf text-danger me-1"></i> ' + escapeHtml(row.filename) + '</td>';
                html += '<td><span class="badge bg-light text-dark border">' + escapeHtml(row.pdf_type) + '</span></td>';
                html += '<td class="font-semibold text-primary">' + (row.matched_product && row.matched_product !== '-' ? escapeHtml(row.matched_product) : '—') + '</td>';
                html += '<td><span class="badge ' + row.badge_class + ' px-2 py-1">' + escapeHtml(row.status) + '</span></td>';
                html += '<td class="text-13 text-muted">' + escapeHtml(row.message) + '</td>';
                html += '</tr>';
            });
        } else {
            html = '<tr><td colspan="6" class="text-center text-muted p-4">No matching results to display.</td></tr>';
        }

        $('#resultsTableBody').html(html);
        $('#resultsWrapper').removeClass('d-none');
        $('html, body').animate({ scrollTop: $('#resultsWrapper').offset().top - 80 }, 500);
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
});
</script>
@endpush
