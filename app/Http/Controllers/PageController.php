<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Certification;
use App\Models\ContactDetail;
use App\Models\ExportCountry;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function about()
    {
        $company = Company::first();
        $certifications = Certification::all();
        return view('pages.about', compact('company', 'certifications'));
    }

    public function certificate()
    {
        $certifications = Certification::all();
        return view('pages.certificate', compact('certifications'));
    }

    public function clients()
    {
        $exportCountries = ExportCountry::all();
        return view('pages.clients', compact('exportCountries'));
    }

    public function contact()
    {
        $contact = ContactDetail::first();
        return view('pages.contact', compact('contact'));
    }

    public function thankYou()
    {
        return view('pages.thank-you');
    }

    public function guide(string $slug)
    {
        return view('pages.guide-details', compact('slug'));
    }
}
