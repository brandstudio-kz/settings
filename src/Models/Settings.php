<?php

namespace BrandStudio\Settings\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use CrudTrait;

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
        return static::findByKey($key)->value;
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
