<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'asset_id',
        'policy_id',
        'asset_name',
        'fingerprint',
        'onchain_metadata',
        'metadata',
        'image_url',
    ];

    protected $casts = [
        'onchain_metadata' => 'array',
        'metadata' => 'array',
    ];

    public function traits()
    {
        return $this->hasMany(AssetTrait::class, 'asset_id', 'asset_id');
    }
}
