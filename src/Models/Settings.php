<?php

namespace BrandStudio\Settings\Models;

use BrandStudio\Starter\Models\Model;

class Settings extends Model
{
    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'settings';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    protected $fillable = [
        'name', 'description', 'field', 'value', 'key'
    ];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public static function get(string $key)
    {
        if (!config('brandstudio.settings.cache_lifetime')) {
            return static::findByKey($key)->value;
        }

        if (\Cache::has("brandstudio_settings_{$key}")) {
            return \Cache::get("brandstudio_settings_{$key}");
        }

        return \Cache::remember("brandstudio_settings_{$key}", config('brandstudio.settings.cache_lifetime'), function() use($key) {
            return static::findByKey($key)->value;
        });
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */
    public function scopeFindByKey($query, string $key)
    {
        return $query->where('key', $key)->firstOrFail();
    }
    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
    public function setValueAttribute($value)
    {
        if ($value instanceof \Illuminate\Http\UploadedFile) {
            $attribute_name = "value";
            $disk = "public";
            $destination_path = "settings";

            $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);
        } else {
            $this->attributes['value'] = $value;
        }
    }
}
