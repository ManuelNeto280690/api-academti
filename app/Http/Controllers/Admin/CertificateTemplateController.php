<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificateTemplate;
use Illuminate\Http\Request;

class CertificateTemplateController extends Controller
{
    public function index()
    {
        return response()->json(CertificateTemplate::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'primary_color' => 'nullable|string|max:20',
            'secondary_color' => 'nullable|string|max:20',
            'background_image' => 'nullable|string',
            'signature_image' => 'nullable|string',
            'signature_name' => 'nullable|string',
            'signature_title' => 'nullable|string',
            'show_logo' => 'boolean',
            'font_family' => 'nullable|string|max:50',
            'layout' => 'nullable|array',
            'is_default' => 'boolean',
        ]);

        $template = CertificateTemplate::create($validated);
        return response()->json($template, 201);
    }

    public function show(CertificateTemplate $certificateTemplate)
    {
        return response()->json($certificateTemplate);
    }

    public function update(Request $request, CertificateTemplate $certificateTemplate)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'primary_color' => 'nullable|string|max:20',
            'secondary_color' => 'nullable|string|max:20',
            'background_image' => 'nullable|string',
            'signature_image' => 'nullable|string',
            'signature_name' => 'nullable|string',
            'signature_title' => 'nullable|string',
            'show_logo' => 'boolean',
            'font_family' => 'nullable|string|max:50',
            'layout' => 'nullable|array',
            'is_default' => 'boolean',
        ]);

        $certificateTemplate->update($validated);
        return response()->json($certificateTemplate);
    }

    public function destroy(CertificateTemplate $certificateTemplate)
    {
        $certificateTemplate->delete();
        return response()->json(null, 204);
    }
}
