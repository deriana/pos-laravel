<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function edit()
    {
        return view('settings.edit');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'required|string|max:255',
            'tax' => 'required|numeric|min:0|max:100',
            'app_logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // file image, max 2MB
        ]);


        Setting::updateOrCreate(['key' => 'APP_NAME'], ['value' => $validated['app_name']]);

        // Simpan TAX sebagai desimal, misal 11% jadi 0.11
        Setting::updateOrCreate(['key' => 'TAX'], ['value' => $validated['tax']]);

        // Jika ada file logo yang diupload
        if ($request->hasFile('app_logo')) {
            $file = $request->file('app_logo');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('img/logo'), $filename);

            // Simpan nama file logo ke database
            Setting::updateOrCreate(['key' => 'APP_LOGO'], ['value' => $filename]);
        }

        Cache::forget('setting_APP_NAME');
        Cache::forget('setting_TAX');
        Cache::forget('setting_APP_LOGO');

        // Artisan::call('config:clear');

        return redirect()->route('settings.edit')->with('success', 'Settings updated successfully.');
    }
}
