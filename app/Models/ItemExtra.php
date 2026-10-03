<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemExtra extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "item_extras";
    protected $fillable = ['item_category_id', 'name', 'status', 'price', 'apply_to_all'];
    protected $casts = [
        'id'               => 'integer',
        'item_category_id' => 'integer',
        'name'             => 'string',
        'status'           => 'integer',
        'price'            => 'decimal:6',
        'apply_to_all'     => 'integer',
    ];

    public function itemCategory()
    {
        return $this->belongsTo(ItemCategory::class, 'item_category_id', 'id');
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_item_extra', 'item_extra_id', 'item_id');
    }
}
