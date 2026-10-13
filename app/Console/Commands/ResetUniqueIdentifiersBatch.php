<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer\Customer;
use Illuminate\Support\Facades\DB;

class ResetUniqueIdentifiersBatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customers:reset-unique-identifiers-batch {--batch-size=1000}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset all unique identifiers to NULL and re-insert them sequentially (batch processing)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $batchSize = $this->option('batch-size');
        $this->info("🔄 Starting unique identifier reset process (batch size: {$batchSize})...");
        
        // Step 1: Check current state
        $totalCustomers = Customer::count();
        $this->info("📊 Total customers: {$totalCustomers}");
        
        if ($totalCustomers === 0) {
            $this->info('ℹ️  No customers found in database.');
            return 0;
        }
        
        // Step 2: Set ALL unique_identifier to NULL using raw SQL for efficiency
        $this->info('🗑️  Setting all unique_identifier to NULL...');
        $updated = DB::statement('UPDATE customers SET unique_identifier = NULL WHERE unique_identifier IS NOT NULL');
        $this->info('✅ All unique_identifier fields are now NULL');
        
        // Step 3: Re-insert unique identifiers in batches
        $this->info('🔄 Re-inserting unique identifiers sequentially (based on ID)...');
        
        $offset = 0;
        $totalProcessed = 0;

        // Determine padding width based on the highest ID
        $maxId = DB::table('customers')->max('id');
        $padWidth = max(3, strlen((string) $maxId));
        
        do {
            // Get batch of customers ordered by ID (oldest ID first)
            $customers = Customer::orderBy('id', 'asc')
                ->offset($offset)
                ->limit($batchSize)
                ->get();
            
            if ($customers->isEmpty()) {
                break;
            }
            
            $this->info("Processing batch: " . ($offset + 1) . " to " . ($offset + $customers->count()));
            
            foreach ($customers as $customer) {
                // Generate unique identifier based on ID: RG-<id> with zero-padding
                $uniqueIdentifier = 'RG-' . str_pad((string) $customer->id, $padWidth, '0', STR_PAD_LEFT);
                
                // Update using raw SQL for better performance
                DB::table('customers')
                    ->where('id', $customer->id)
                    ->update(['unique_identifier' => $uniqueIdentifier]);
                
                $totalProcessed++;
            }
            
            $offset += $batchSize;
            
            // Show progress
            $percentage = round(($totalProcessed / $totalCustomers) * 100, 2);
            $this->info("Progress: {$totalProcessed}/{$totalCustomers} ({$percentage}%)");
            
        } while ($customers->count() === $batchSize);
        
        // Step 4: Verify the results
        $this->info('🔍 Verifying results...');
        
        $finalTotal = Customer::count();
        $finalWithIdentifiers = Customer::whereNotNull('unique_identifier')->count();
        $finalUniqueIdentifiers = DB::table('customers')
            ->whereNotNull('unique_identifier')
            ->distinct('unique_identifier')
            ->count('unique_identifier');
        
        $this->info("📊 Final state:");
        $this->info("   Total customers: {$finalTotal}");
        $this->info("   Customers with identifiers: {$finalWithIdentifiers}");
        $this->info("   Unique identifiers: {$finalUniqueIdentifiers}");
        
        // Check for duplicates
        if ($finalWithIdentifiers === $finalUniqueIdentifiers) {
            $this->info('✅ SUCCESS: All identifiers are unique!');
        } else {
            $duplicates = $finalWithIdentifiers - $finalUniqueIdentifiers;
            $this->error("❌ ERROR: Found {$duplicates} duplicate identifiers!");
            return 1;
        }
        
        // Step 5: Show examples
        $this->info('📋 Examples of updated records:');
        $examples = Customer::whereNotNull('unique_identifier')
            ->orderBy('id', 'asc')
            ->take(5)
            ->get(['id', 'unique_identifier', 'mteja', 'created_at']);
        
        foreach ($examples as $customer) {
            $this->line("ID: {$customer->id} | Unique ID: {$customer->unique_identifier} | Mteja: {$customer->mteja} | Created: {$customer->created_at}");
        }
        
        // Step 6: Show latest identifier
        $latestCustomer = Customer::whereNotNull('unique_identifier')
            ->orderBy('unique_identifier', 'desc')
            ->first();
        
        if ($latestCustomer) {
            $this->info("🎯 Latest unique identifier: {$latestCustomer->unique_identifier}");
            $number = (int) substr($latestCustomer->unique_identifier, 3);
            $nextNumber = $number + 1;
            $nextIdentifier = 'RG-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            $this->info("   Next new entry will get: {$nextIdentifier}");
        }
        
        $this->info('🎉 Reset process completed successfully!');
        return 0;
    }
}
