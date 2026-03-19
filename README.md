# CNFT Lens — A lightweight, Laravel-native REST API

CNFT Lens turns raw Cardano data into clean, developer-friendly NFT responses for PHP teams (particularly aimed at Kenya/Africa).

## PROJECT MISSION

- **Purpose**: Provide fast, simple endpoints that return enriched Cardano NFT data (clean image URLs, trait-aware filtering, cached responses, transaction history) so PHP developers can integrate Cardano NFTs without glue code or running a node.
- **X-factor**: Instant developer joy — usable responses out of the box so teams can build viewers, analytics, or micro-frontends within minutes.

> "I’m building CNFT Lens to make Cardano NFTs effortless for PHP developers — one clear endpoint at a time."

---

## CORE ENDPOINTS

- `GET /api/nfts/policy/{policy_id}`: Paginated list of NFTs in a collection.
- `GET /api/nfts/policy/{policy_id}?trait={trait}&value={value}`: Server-side trait filtering.
- `GET /api/nfts/asset/{asset_id}`: Full NFT details (normalized metadata, canonical image URL, CIP-25/CIP-68 support).
- `GET /api/nfts/asset/{asset_id}/history`: Transaction history (sales vs transfers).

## TECHNICAL STACK

- **Laravel**: Services, API Resources, Caching, Rate Limiting, Sanctum.
- **Cardano SDK**: Blockfrost API integration.
- **Metadata**: Support for CIP-25 and CIP-68 standards.
- **Images**: Canonical mapping via [nftcdn.io](https://nftcdn.io).

## LOCAL SETUP (PHASE 1)

```bash
cd cnft-lens
composer install
cp .env.example .env
php artisan key:generate
php artisan serve
```

---

## DEVELOPMENT PHASES

- **Phase 1 (MV API)**: Basic endpoints with Blockfrost-backed data.
- **Phase 2 (Trait filtering)**: Server-side trait parsing and pagination.
- **Phase 3 (Caching & history)**: Performance layer and transaction logic.
- **Phase 4 (Extras)**: Collection stats, rarity, and documentation.

## NON-GOALS

- Do not build a full marketplace, minting tools, or multi-chain support.
- Do not run a Cardano node.
- Avoid over-engineering — prefer simple, well-tested code.
