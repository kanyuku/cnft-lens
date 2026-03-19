<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NftResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $onChainMetadata = data_get($this->resource, 'onchain_metadata', []);
        $metadata = data_get($this->resource, 'metadata', []);

        $assetId = data_get($this->resource, 'asset', data_get($this->resource, 'asset_id'));

        return [
            'asset_id' => $assetId,
            'policy_id' => data_get($this->resource, 'policy_id'),
            'asset_name' => data_get($this->resource, 'asset_name'),
            'fingerprint' => data_get($this->resource, 'fingerprint'),
            'quantity' => data_get($this->resource, 'quantity'),
            'image_url' => $this->resolveImageUrl($onChainMetadata, $assetId),
            'metadata' => $this->normalizeMetadata($onChainMetadata, $metadata),
            'traits' => $this->extractTraits($onChainMetadata),
        ];
    }

    protected function resolveImageUrl($onChainMetadata, $assetId): ?string
    {
        // Preferred: nftcdn.io canonical URL if assetId is present
        if ($assetId) {
            return "https://mainnet.nftcdn.io/mainnet/{$assetId}?width=600";
        }

        // Fallback: Parse from metadata (IPFS etc)
        $image = $onChainMetadata['image'] ?? null;

        if (is_array($image)) {
            $image = implode('', $image);
        }

        if (is_string($image)) {
            if (str_starts_with($image, 'ipfs://')) {
                return str_replace('ipfs://', 'https://ipfs.io/ipfs/', $image);
            }
        }

        return $image;
    }

    protected function normalizeMetadata($onChain, $offChain): array
    {
        return array_merge($offChain ?? [], $onChain ?? []);
    }

    protected function extractTraits($metadata): array
    {
        $traits = [];

        // common keys for traits in CIP-25
        $traitKeys = ['attributes', 'traits'];

        foreach ($traitKeys as $key) {
            if (isset($metadata[$key]) && is_array($metadata[$key])) {
                return $metadata[$key];
            }
        }

        // Sometimes they are top-level excluding standard CIP-25 keys
        $standardKeys = ['name', 'image', 'mediaType', 'description', 'files'];
        foreach ($metadata as $key => $value) {
            if (!in_array($key, $standardKeys) && !is_array($value)) {
                $traits[$key] = $value;
            }
        }

        return $traits;
    }
}
