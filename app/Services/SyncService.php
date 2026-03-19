<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetTrait;
use Illuminate\Support\Facades\Log;

class SyncService
{
    protected CardanoService $cardano;

    public function __construct(CardanoService $cardano)
    {
        $this->cardano = $cardano;
    }

    /**
     * Sync all assets and traits for a given policy ID.
     */
    public function syncPolicy(string $policyId): void
    {
        $page = 1;
        $count = 100;

        do {
            $assets = $this->cardano->getAssetsByPolicy($policyId, $page, $count);

            if (!$assets || count($assets) === 0) {
                break;
            }

            foreach ($assets as $assetInfo) {
                $assetId = $assetInfo['asset'];

                // Skip if already synced (naive check)
                if (Asset::where('asset_id', $assetId)->exists()) {
                    continue;
                }

                $this->syncAsset($assetId);
            }

            $page++;
        } while (count($assets) === $count);
    }

    /**
     * Sync detailed info for a specific asset.
     */
    public function syncAsset(string $assetId): ?Asset
    {
        $details = $this->cardano->getAssetDetails($assetId);

        if (!$details) {
            return null;
        }

        $asset = Asset::updateOrCreate(
            ['asset_id' => $assetId],
            [
                'policy_id' => $details['policy_id'],
                'asset_name' => $details['asset_name'],
                'fingerprint' => $details['fingerprint'] ?? null,
                'onchain_metadata' => $details['onchain_metadata'] ?? null,
                'metadata' => $details['metadata'] ?? null,
                'image_url' => $this->resolveImageUrl($details),
            ]
        );

        // Sync traits
        $this->syncTraits($asset, $details['onchain_metadata'] ?? []);

        return $asset;
    }

    protected function syncTraits(Asset $asset, array $metadata): void
    {
        // First clear existing traits
        $asset->traits()->delete();

        $traits = $this->extractTraits($metadata);

        foreach ($traits as $type => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }

            AssetTrait::create([
                'asset_id' => $asset->asset_id,
                'trait_type' => (string) $type,
                'value' => (string) $value,
            ]);
        }
    }

    protected function resolveImageUrl(array $details): ?string
    {
        return "https://mainnet.nftcdn.io/mainnet/{$details['asset']}?width=600";
    }

    protected function extractTraits(array $metadata): array
    {
        $traits = [];
        $traitKeys = ['attributes', 'traits'];

        foreach ($traitKeys as $key) {
            if (isset($metadata[$key]) && is_array($metadata[$key])) {
                return $metadata[$key];
            }
        }

        $standardKeys = ['name', 'image', 'mediaType', 'description', 'files'];
        foreach ($metadata as $key => $value) {
            if (!in_array($key, $standardKeys) && !is_array($value)) {
                $traits[$key] = $value;
            }
        }

        return $traits;
    }
}
