<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        return response()->json(Setting::all()->pluck('value', 'key'));
    }

    public function update(Request $request)
    {
        $settings = $request->all();
        foreach ($settings as $key => $value) {
            Setting::set($key, (string)$value);
        }

        return response()->json(['message' => 'Configurações atualizadas com sucesso']);
    }
}
