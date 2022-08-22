<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable=['product_name','description','slug','section_id'];
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function section(){
        return $this->belongsTo(Section::class);
    }
    protected static function booted(){
        static::creating(function (Product $product) {
            $slug = slug($product->product_name);
            $count = Product::where('slug', 'LIKE', "{$slug}%")->count();
            if ($count) {
                $slug .= '-' . ($count + 1);
            }
            $product->slug = $slug;
        });

        static::updating(function(Product $product){
            $slug = slug($product->product_name);
            $count = Product::where('slug', 'LIKE', "{$slug}%")->count();
            if ($count>1) {
                $slug .= '-' . ($count + 1);
            }
            $product->slug = $slug;
        });
    }
}
