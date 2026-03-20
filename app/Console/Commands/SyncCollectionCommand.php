<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

class SyncCollectionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nfts:sync {policy_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all assets for a given policy ID from Blockfrost to the local database';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\SyncService $syncService)
    {
        $policyId = $this->argument('policy_id');

        $this->info("Starting sync for policy: {$policyId}");

        $countBefore = \App\Models\Asset::where('policy_id', $policyId)->count();

        $syncService->syncPolicy($policyId);

        $countAfter = \App\Models\Asset::where('policy_id', $policyId)->count();
        $syncedCount = $countAfter - $countBefore;

        $this->info("Sync completed for policy: {$policyId}. Synced {$syncedCount} new assets.");
        $this->info("Total assets now in database for this policy: {$countAfter}");

        return Command::SUCCESS;
    }
}
