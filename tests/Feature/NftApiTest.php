<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\AssetTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NftApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Asset::unguard();
        AssetTrait::unguard();
    }

    public function test_can_get_policy_assets(): void
    {
        Asset::create([
            'asset_id' => 'test_asset_1',
            'policy_id' => 'test_policy',
            'asset_name' => 'Test NFT #1',
        ]);

        $response = $this->getJson('/api/nfts/policy/test_policy');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['asset_id', 'policy_id', 'asset_name', 'image_url']
                ]
            ]);
    }

    public function test_can_filter_by_trait(): void
    {
        Asset::create([
            'asset_id' => 'blue_asset',
            'policy_id' => 'test_policy',
            'asset_name' => 'Blue NFT',
        ]);

        AssetTrait::create([
            'asset_id' => 'blue_asset',
            'trait_type' => 'Background',
            'value' => 'Blue',
        ]);

        $response = $this->getJson('/api/nfts/policy/test_policy?trait=Background&value=Blue');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['asset_id' => 'blue_asset']);
    }

    public function test_can_get_collection_stats(): void
    {
        Asset::create([
            'asset_id' => 'test_asset',
            'policy_id' => 'test_policy',
            'asset_name' => 'Test NFT',
        ]);

        AssetTrait::create([
            'asset_id' => 'test_asset',
            'trait_type' => 'Eyes',
            'value' => 'Red',
        ]);

        $response = $this->getJson('/api/nfts/policy/test_policy/stats');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'total_assets',
                    'rarity' => [
                        '*' => ['trait_type', 'value', 'count', 'percentage']
                    ]
                ]
            ]);
    }
}
