# CNFT Lens
### A lightweight, Laravel-native REST API for Cardano NFTs

CNFT Lens turns raw Cardano data into clean, developer-friendly NFT responses for PHP teams (particularly aimed at Kenya/Africa). Integrate Cardano NFTs into your viewers, analytics, or micro-frontends within minutes.

---

## Project Mission
- **Purpose**: Provide fast, simple endpoints that return enriched Cardano NFT data (clean image URLs, trait-aware filtering, cached responses, transaction history) so PHP developers can integrate Cardano NFTs without glue code or running a node.
- **X-factor**: **Instant developer joy** — usable responses out of the box.

> *"I’m building CNFT Lens to make Cardano NFTs effortless for PHP developers — one clear endpoint at a time."*

---

## Features

- **Canonical Image Resolution**: Automatically maps raw metadata to premium [nftcdn.io](https://nftcdn.io) URLs.
- **Trait Filtering**: Server-side filtering by attributes (e.g., `Background=Blue`).
- **High-Performance Caching**: Layered caching (1h-24h) to protect against Blockfrost rate limits.
- **Normalized Metadata**: Consistent CIP-25 and CIP-68 support.
- **Collection Stats**: Trait rarity analysis and item counts.
- **Auto-Generated Docs**: Scribe integration for instant API documentation and Postman exports.

---

## Local Setup

### Prerequisites
- PHP 8.3+
- Composer
- SQLite (default)
- [Blockfrost API Key](https://blockfrost.io/)

### Installation

```bash
# Clone and enter the directory
git clone https://github.com/kanyuku/cnft-lens.git
cd cnft-lens

# Install dependencies
composer install

# Environment configuration
cp .env.example .env
php artisan key:generate

# Database setup
touch database/database.sqlite
php artisan migrate
```

### Configuration
Update your `.env` with your Blockfrost credentials:
```env
BLOCKFROST_PROJECT_ID=mainnet_your_key_here
BLOCKFROST_NETWORK=mainnet
```

---

## Core Endpoints

### Collections
- `GET /api/nfts/policy/{policy_id}`: Paginated list of NFTs.
- `GET /api/nfts/policy/{policy_id}?trait=Eyes&value=Red`: Filtered asset list.
- `GET /api/nfts/policy/{policy_id}/stats`: Rarity hints and collection aggregates.

### Assets
- `GET /api/nfts/asset/{asset_id}`: Full details, canonical image URL, and normalized traits.
- `GET /api/nfts/asset/{asset_id}/history`: Transaction history for the asset.

---

## Syncing Collections

To enable **trait filtering** and **rarity statistics**, you must sync a collection to your local database:

```bash
php artisan nfts:sync {policy_id}
```
*Note: Large collections may take a few minutes depending on your Blockfrost plan.*

---

## Documentation & Testing

- **API Docs**: Visit `/docs` on your local server to view auto-generated documentation.
- **Testing**: Run `php artisan test` to verify the API health.

---

## Non-Goals
- No full marketplace or minting tools.
- No local Cardano node required.
- Minimalist, well-tested code over complex abstractions.

---

Built for the Cardano community in Kenya.
