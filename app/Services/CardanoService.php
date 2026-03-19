<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CardanoService
{
    protected string $projectId;
    protected string $baseUrl;

    public function __construct()
    {
        $this->projectId = config('cardano.blockfrost.project_id');
        $this->baseUrl = config('cardano.blockfrost.base_url');
    }

    public function getAssetsByPolicy(string $policyId, int $page = 1, int $count = 100)
    {
        $response = Http::withHeaders([
            'project_id' => $this->projectId,
        ])->get("{$this->baseUrl}/assets/policy/{$policyId}", [
                    'page' => $page,
                    'count' => $count,
                ]);

        if ($response->failed()) {
            Log::error("Blockfrost API error (Policy): " . $response->body());
            return null;
        }

        return $response->json();
    }

    public function getAssetDetails(string $assetId)
    {
        $response = Http::withHeaders([
            'project_id' => $this->projectId,
        ])->get("{$this->baseUrl}/assets/{$assetId}");

        if ($response->failed()) {
            Log::error("Blockfrost API error (Asset): " . $response->body());
            return null;
        }

        return $response->json();
    }

    public function getAssetHistory(string $assetId)
    {
        $response = Http::withHeaders([
            'project_id' => $this->projectId,
        ])->get("{$this->baseUrl}/assets/{$assetId}/history");

        if ($response->failed()) {
            Log::error("Blockfrost API error (History): " . $response->body());
            return null;
        }

        return $response->json();
    }
}
