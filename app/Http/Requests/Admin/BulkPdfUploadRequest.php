<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BulkPdfUploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'pdf_type' => 'required|string|in:msds,specification',
            'existing_mode' => 'required|string|in:skip,replace',
            'pdf_files' => 'required|array|min:1',
            'pdf_files.*' => 'required|file|mimes:pdf|max:20480',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'pdf_type.required' => 'Please select a valid PDF type (MSDS or Specification).',
            'pdf_type.in' => 'PDF type must be either MSDS or Specification.',
            'existing_mode.required' => 'Please select how to handle existing PDFs (Skip or Replace).',
            'existing_mode.in' => 'Existing PDF handling mode must be either Skip or Replace.',
            'pdf_files.required' => 'Please upload at least one PDF file.',
            'pdf_files.min' => 'Please select at least one PDF file to upload.',
            'pdf_files.*.file' => 'Uploaded file is invalid.',
            'pdf_files.*.mimes' => 'Only PDF files (.pdf) are allowed. Non-PDF files are rejected.',
            'pdf_files.*.max' => 'Each PDF file must not exceed 20 MB.',
        ];
    }
}
