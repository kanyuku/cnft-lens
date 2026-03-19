<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NftResource;
use App\Models\Asset;
use App\Models\AssetTrait;
use App\Services\CardanoService;
use App\Services\SyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * @group NFTs
 * 
 * Endpoints for interacting with Cardano NFTs.
 */
class NftController extends Controller
{
    protected CardanoService $cardano;
    protected SyncService $sync;

    public function __construct(CardanoService $cardano, SyncService $sync)
    {
        $this->cardano = $cardano;
        $this->sync = $sync;
    }

    /**
     * Get Collection Assets
     * 
     * Returns a paginated list of NFTs in a collection, with optional trait filtering.
     * 
     * @urlParam policy_id string Required. The policy ID of the collection.
     * @queryParam trait string Filter by trait type (e.g. Background).
     * @queryParam value string Filter by trait value (e.g. Blue).
     */
    public function policy(Request $request, string $policyId)
    {
        $trait = $request->query('trait');
        $value = $request->query('value');
        $page = $request->query('page', 1);
        $count = $request->query('count', 100);

        $cacheKey = "policy.{$policyId}.{$trait}.{$value}.{$page}.{$count}";

        return Cache::remember($cacheKey, now()->addHour(), function () use ($policyId, $trait, $value, $page, $count, $request) {
            $query = Asset::where('policy_id', $policyId);

            if ($trait && $value) {
                $query->whereHas('traits', function ($q) use ($trait, $value) {
                    $q->where('trait_type', $trait)->where('value', $value);
                });
            }

            $assets = $query->paginate($request->query('count', 100));

            if ($assets->isEmpty() && !$trait) {
                $bfAssets = $this->cardano->getAssetsByPolicy($policyId, $page, $request->query('count', 100));
                if ($bfAssets) {
                    return [
                        'data' => $bfAssets,
                        'meta' => [
                            'note' => 'Results from live API. Sync collection via CLI for trait filtering.',
                        ]
                    ];
                }
            }

            return NftResource::collection($assets)->response()->getData(true);
        });
    }

    public function asset(string $assetId)
    {
        return Cache::remember("asset.{$assetId}", now()->addDay(), function () use ($assetId) {
            $asset = Asset::where('asset_id', $assetId)->first();

            if (!$asset) {
                $asset = $this->sync->syncAsset($assetId);
            }

            if (!$asset) {
                return response()->json(['error' => 'Asset not found'], 404);
            }

            return new NftResource($asset);
        });
    }

    public function history(string $assetId)
    {
        return Cache::remember("history.{$assetId}", now()->addHour(), function () use ($assetId) {
            $history = $this->cardano->getAssetHistory($assetId);

            if (!$history) {
                return response()->json(['error' => 'History not found'], 404);
            }

            $mappedHistory = collect($history)->map(function ($tx) {
                return [
                    'tx_hash' => $tx['tx_hash'],
                    'action_at' => $tx['action_at'] ?? null,
                    'type' => $this->detectTxType($tx),
                ];
            });

            return ['data' => $mappedHistory];
        });
    }

    public function stats(string $policyId)
    {
        return Cache::remember("stats.{$policyId}", now()->addHours(6), function () use ($policyId) {
            $total = Asset::where('policy_id', $policyId)->count();

            if ($total === 0) {
                return response()->json(['error' => 'Collection not synced'], 404);
            }

            $rarity = DB::table('asset_traits')
                ->join('assets', 'assets.asset_id', '=', 'asset_traits.asset_id')
                ->where('assets.policy_id', $policyId)
                ->select('trait_type', 'value', DB::raw('count(*) as count'))
                ->groupBy('trait_type', 'value')
                ->get()
                ->map(function ($item) use ($total) {
                    $item->percentage = round(($item->count / $total) * 100, 2);
                    return $item;
                });

            return [
                'data' => [
                    'policy_id' => $policyId,
                    'total_assets' => $total,
                    'rarity' => $rarity,
                ]
            ];
        });
    }

    protected function detectTxType($tx): string
    {
        // Simple heuristic: sales usually involve marketplace contract interactions.
        // In a real app, we'd check the marketplace contract addresses.
        return 'transfer';
    }
}
