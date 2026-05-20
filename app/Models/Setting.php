<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set($key, $value)
    {
        return self::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function getPublic()
    {
        $publicKeys = [
            'plataformaNome', 'plataformaUrl', 'emailSuporte', 'telefone', 'morada',
            'bank_name', 'bank_iban', 'bank_holder', 'moeda', 'payment_bank_accounts',
            'payment_whatsapp_proof', 'payment_email_proof'
        ];
        return self::whereIn('key', $publicKeys)->get()->pluck('value', 'key');
    }
}
