<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomepageSetting extends Model
{
    use HasFactory;

    protected $table = 'homepage_settings';
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Get a setting value by key, returning default if not found
     */
    public static function getByKey(string $key, $default = null)
    {
        $setting = self::find($key);
        if (!$setting) {
            return $default;
        }

        // Try decoding as JSON, if it fails, return raw string value
        $decoded = json_decode($setting->value, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        return $setting->value;
    }

    /**
     * Set a setting value by key
     */
    public static function setByKey(string $key, $value): self
    {
        $serializedValue = is_array($value) || is_object($value) ? json_encode($value) : $value;
        
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $serializedValue]
        );
    }
}
