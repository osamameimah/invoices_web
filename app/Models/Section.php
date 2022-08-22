<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class Section extends Model
{
    use HasFactory;

    protected $fillable=['section_name','description','created_by','slug'];
    public function getRouteKeyName()
    {
        return 'slug';
    }
    
    public function products(){
        return $this->hasMany(Product::class);
    }

    protected static function booted(){
        static::creating(function (Section $section) {
            $section->created_by =auth()->id();
            $slug = slug($section->section_name);
            $count = Section::where('slug', 'LIKE', "{$slug}%")->count();
            if ($count) {
                $slug .= '-' . ($count + 1);
            }
            $section->slug = $slug;
        });

        static::updating(function(Section $section){

            $slug = slug($section->section_name);
            $count = Section::where('slug', 'LIKE', "{$slug}%")->count();
            if ($count>1) {
                $slug .= '-' . ($count + 1);
            }
            $section->slug = $slug;
        });
    }
}
