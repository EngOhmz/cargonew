<?php

namespace App\Http\Controllers;

use App\Models\Sms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SmsController extends Controller
{
    /**
     * Display company-specific SMS templates.
     */
    public function index()
    {
        $companyId = Auth::user()->company_id ?? null;
        if (!$companyId) {
            return redirect()->back()->with('error', 'Kampuni haijatajwa. Haiwezekani kusimamia SMS templates.');
        }

        $sms_list = Sms::ensureCompanyTemplates($companyId);

        return view('sms.index', compact('sms_list'));
    }

    /**
     * Edit one of this company's templates.
     */
    public function edit($id)
    {
        $companyId = Auth::user()->company_id ?? null;
        abort_unless($companyId, 403);

        $sms = Sms::forCompany($companyId)->where('id', $id)->firstOrFail();

        return view('sms.edit', compact('sms'));
    }

    /**
     * Update only this company's template (does not affect other companies).
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $companyId = Auth::user()->company_id ?? null;
        abort_unless($companyId, 403);

        $sms = Sms::forCompany($companyId)->where('id', $id)->firstOrFail();
        $sms->message = $request->message;
        $sms->save();

        return redirect()->route('sms.index')->with('success', 'SMS updated successfully');
    }
}
