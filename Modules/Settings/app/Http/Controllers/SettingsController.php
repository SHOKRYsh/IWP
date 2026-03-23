<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Modules\Settings\Models\Setting;

class SettingsController extends Controller
{
    public function index()
    {
        $setting = Setting::firstOrCreate([], [
            'application_name' => 'IWP',
            'privacy_policy' => '',
            'terms_of_services' => '',
        ]);

        return $this->respondOk($setting, 'Settings retrieved successfully');
    }

    public function update(Request $request)
    {
        $setting = Setting::first();

        if (!$setting) {
            $setting = new Setting();
        }

        $validated = $request->validate([
            'application_name' => 'sometimes|required|string|max:255',
            'privacy_policy' => 'nullable|string',
            'terms_of_services' => 'nullable|string',
        ]);

        $setting->fill($validated);
        $setting->save();

        return $this->respondOk($setting, 'Settings updated successfully');
    }
}
