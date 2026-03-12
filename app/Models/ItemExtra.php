<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemExtra extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "item_extras";
    protected $fillable = ['item_category_id', 'name', 'status', 'price'];
    protected $casts = [
        'id'               => 'integer',
        'item_category_id' => 'integer',
        'name'             => 'string',
        'status'           => 'integer',
        'price'            => 'decimal:6',
    ];

    public function itemCategory()
    {
        return $this->belongsTo(ItemCategory::class, 'item_category_id', 'id');
    }
}
