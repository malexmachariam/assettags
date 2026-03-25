<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category_id',
        'model_no',
        'require_serial_number',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}