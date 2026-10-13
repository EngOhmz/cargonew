<?php

namespace App\Http\Controllers;

use App\Models\DocumentSetting;
use Illuminate\Http\Request;

class DocumentSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $companyId = auth()->user()->company_id ?? null;
        $doc = DocumentSetting::forCompany($companyId);

        return view('access_control.document_settings', [
            'doc' => $doc,
            'companyId' => $companyId,
        ]);
    }

    public function update(Request $request)
    {
        $companyId = auth()->user()->company_id ?? null;

        $request->validate([
            'header_line1' => 'nullable|string|max:255',
            'header_line2' => 'nullable|string|max:255',
            'office_1_label' => 'nullable|string|max:100',
            'office_1_place' => 'nullable|string|max:100',
            'office_1_contact' => 'nullable|string|max:100',
            'office_1_contact_2' => 'nullable|string|max:100',
            'office_1_contact_3' => 'nullable|string|max:100',
            'office_2_label' => 'nullable|string|max:100',
            'office_2_place' => 'nullable|string|max:100',
            'office_2_contact' => 'nullable|string|max:100',
            'office_3_label' => 'nullable|string|max:100',
            'office_3_place' => 'nullable|string|max:100',
            'office_3_contact' => 'nullable|string|max:100',
            'kanuni_1' => 'nullable|string',
            'kanuni_2' => 'nullable|string',
            'kanuni_3' => 'nullable|string',
            'kanuni_4' => 'nullable|string',
            'logo_type' => 'nullable|string|in:emasuite,system',
            'system_logo_title' => 'nullable|string|max:255',
            'tin_number' => 'nullable|string|max:50',
        ]);

        $doc = DocumentSetting::where('company_id', $companyId)->first();
        $data = $request->only([
            'header_line1', 'header_line2',
            'office_1_label', 'office_1_place', 'office_1_contact', 'office_1_contact_2', 'office_1_contact_3',
            'office_2_label', 'office_2_place', 'office_2_contact',
            'office_3_label', 'office_3_place', 'office_3_contact',
            'kanuni_1', 'kanuni_2', 'kanuni_3', 'kanuni_4',
            'logo_type', 'system_logo_title', 'tin_number',
        ]);

        if ($doc) {
            $doc->update($data);
        } else {
            $data['company_id'] = $companyId;
            DocumentSetting::create($data);
        }

        return redirect()->route('document_settings.index')->with('success', 'Document settings saved.');
    }
}
