<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branding;
use App\Models\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrandingController extends Controller
{
    public function show(Request $request)
    {
        $data = [
            'site_logo' => Branding::getValue('site_logo'),
            'site_favicon' => Branding::getValue('site_favicon'),
            'meta_title' => Branding::getValue('meta_title'),
            'meta_description' => Branding::getValue('meta_description'),
            'meta_keywords' => Branding::getValue('meta_keywords'),
        ];

        // Convert stored paths to full URLs for frontend previews
        if (!empty($data['site_logo'])) {
            $data['site_logo_url'] = asset('storage/' . ltrim($data['site_logo'], '/'));
        }
        if (!empty($data['site_favicon'])) {
            $data['site_favicon_url'] = asset('storage/' . ltrim($data['site_favicon'], '/'));
        }

        return response()->json($data);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:500'],
            'site_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'site_favicon' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,ico', 'max:2048'],
        ]);

        // Handle file uploads
        if ($request->hasFile('site_logo')) {
            $old = Branding::getValue('site_logo');
            $path = $request->file('site_logo')->store('branding', 'public');
            Branding::setValue('site_logo', $path, $request->user()->id);
            if ($old) Storage::disk('public')->delete($old);
        }

        if ($request->hasFile('site_favicon')) {
            $old = Branding::getValue('site_favicon');
            $path = $request->file('site_favicon')->store('branding', 'public');
            Branding::setValue('site_favicon', $path, $request->user()->id);
            if ($old) Storage::disk('public')->delete($old);
        }

        // Text values
        Branding::setValue('meta_title', $validated['meta_title'] ?? null, $request->user()->id);
        Branding::setValue('meta_description', $validated['meta_description'] ?? null, $request->user()->id);
        Branding::setValue('meta_keywords', $validated['meta_keywords'] ?? null, $request->user()->id);

        UserLog::create([
            'user_id' => $request->user()->id,
            'type' => 'branding_update',
            'ip' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'meta' => null,
        ]);

        return $this->show($request);
    }
}


