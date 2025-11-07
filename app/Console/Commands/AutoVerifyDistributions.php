<?php

namespace App\Console\Commands;

use App\Models\SupplyDistribution;
use App\Models\SupplyRequest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AutoVerifyDistributions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'supplies:auto-verify-distributions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically verify distributions that have been pending for more than 7 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting auto-verification of pending distributions...');

        // Get distributions pending for more than 7 days
        $pendingDistributions = SupplyDistribution::with(['requestItem.request'])
            ->where('verification_status', 'pending')
            ->where('created_at', '<=', now()->subDays(7))
            ->get();

        if ($pendingDistributions->isEmpty()) {
            $this->info('No distributions found pending for more than 7 days.');
            return 0;
        }

        $this->info("Found {$pendingDistributions->count()} distributions to auto-verify.");

        $verifiedCount = 0;
        $skippedCount = 0;

        DB::beginTransaction();

        try {
            foreach ($pendingDistributions as $distribution) {
                // Verify the distribution with system user (null or system user)
                $distribution->update([
                    'verification_status' => 'verified',
                    'verified_by' => null, // System auto-verification
                    'verified_at' => now(),
                    'verification_notes' => 'Automatically verified after 7 days of pending verification.',
                ]);

                // Check if all distributions for this request are verified
                $request = $distribution->requestItem?->request;
                if ($request) {
                    $request->load(['items' => function ($query) {
                        $query->with('distributions');
                    }]);

                    $allVerified = true;
                    foreach ($request->items as $item) {
                        $itemDistributions = $item->distributions;
                        $nonVerifiedDistributions = $itemDistributions->where('verification_status', '!=', 'verified');
                        if ($nonVerifiedDistributions->isNotEmpty()) {
                            $allVerified = false;
                            break;
                        }
                    }

                    if ($allVerified) {
                        // All distributions verified, check if all items are completed
                        $allItemsCompleted = $request->items->every(function ($item) {
                            return $item->fulfillment_status === 'completed';
                        });

                        $request->update([
                            'status' => $allItemsCompleted ? 'fulfilled' : 'partially_fulfilled',
                        ]);
                    }
                }

                $verifiedCount++;
                $this->line("Auto-verified distribution #{$distribution->form_number} (ID: {$distribution->id})");
            }

            DB::commit();

            $this->info("Successfully auto-verified {$verifiedCount} distributions.");
            $this->info("Skipped {$skippedCount} distributions.");

            return 0;
        } catch (\Exception $e) {
            DB::rollBack();

            $this->error("Failed to auto-verify distributions: {$e->getMessage()}");
            $this->error($e->getTraceAsString());

            return 1;
        }
    }
}
