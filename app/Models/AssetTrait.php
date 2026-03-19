<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetTrait extends Model
{
    protected $fillable = [
        'asset_id',
        'trait_type',
        'value'
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id', 'asset_id');
    }
}
