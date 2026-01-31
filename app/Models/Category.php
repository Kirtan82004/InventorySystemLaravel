<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status'
    ];

    // Relationship: One Category has many Products
    public function products() {
        return $this->hasMany(Product::class);
    }
}
