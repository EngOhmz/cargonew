<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer\Customer;
use Illuminate\Support\Facades\DB;

class ResetUniqueIdentifiers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customers:reset-unique-identifiers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset all unique identifiers to NULL and re-insert them sequentially';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🔄 Starting unique identifier reset process...');
        
        // Step 1: Check current state
        $totalCustomers = Customer::count();
        $customersWithIdentifiers = Customer::whereNotNull('unique_identifier')->count();
        
        $this->info("📊 Current state:");
        $this->info("   Total customers: {$totalCustomers}");
        $this->info("   Customers with identifiers: {$customersWithIdentifiers}");
        
        if ($totalCustomers === 0) {
            $this->info('ℹ️  No customers found in database.');
            return 0;
        }
        
        // Step 2: Set ALL unique_identifier to NULL
        $this->info('🗑️  Setting all unique_identifier to NULL...');
        $updated = Customer::whereNotNull('unique_identifier')->update(['unique_identifier' => null]);
        $this->info("   Updated {$updated} records to NULL");
        
        // Step 3: Verify all are NULL
        $remainingWithIdentifiers = Customer::whereNotNull('unique_identifier')->count();
        if ($remainingWithIdentifiers > 0) {
            $this->error("❌ ERROR: {$remainingWithIdentifiers} records still have identifiers!");
            return 1;
        }
        $this->info('✅ All unique_identifier fields are now NULL');
        
        // Step 4: Re-insert unique identifiers sequentially
        $this->info('🔄 Re-inserting unique identifiers sequentially...');
        
        // Get all customers ordered by creation date (oldest first)
        $customers = Customer::orderBy('created_at', 'asc')->get();
        
        $progressBar = $this->output->createProgressBar($customers->count());
        $progressBar->start();
        
        $counter = 1;
        $successCount = 0;
        
        foreach ($customers as $customer) {
            try {
                // Generate unique identifier: RG-001, RG-002, etc.
                $uniqueIdentifier = 'RG-' . str_pad($counter, 3, '0', STR_PAD_LEFT);
                
                // Update the customer record
                $customer->update([
                    'unique_identifier' => $uniqueIdentifier
                ]);
                
                $successCount++;
                $counter++;
                
            } catch (\Exception $e) {
                $this->error("\n❌ Error updating customer ID {$customer->id}: " . $e->getMessage());
            }
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine();
        
        // Step 5: Verify the results
        $this->info('🔍 Verifying results...');
        
        $finalTotal = Customer::count();
        $finalWithIdentifiers = Customer::whereNotNull('unique_identifier')->count();
        $finalUniqueIdentifiers = Customer::whereNotNull('unique_identifier')
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
        
        // Step 6: Show examples
        $this->info('📋 Examples of updated records:');
        $this->table(
            ['ID', 'Unique ID', 'Mteja', 'Created At'],
            Customer::whereNotNull('unique_identifier')
                ->orderBy('created_at', 'asc')
                ->take(10)
                ->get(['id', 'unique_identifier', 'mteja', 'created_at'])
                ->map(function($customer) {
                    return [
                        $customer->id,
                        $customer->unique_identifier,
                        $customer->mteja,
                        $customer->created_at->format('Y-m-d H:i:s')
                    ];
                })
        );
        
        // Step 7: Show latest identifier
        $latestCustomer = Customer::whereNotNull('unique_identifier')
            ->orderBy('unique_identifier', 'desc')
            ->first();
        
        if ($latestCustomer) {
            $this->info("🎯 Latest unique identifier: {$latestCustomer->unique_identifier}");
            $this->info("   Next new entry will get: " . $this->getNextIdentifier($latestCustomer->unique_identifier));
        }
        
        $this->info('🎉 Reset process completed successfully!');
        return 0;
    }
    
    /**
     * Get the next identifier after the given one
     */
    private function getNextIdentifier($currentIdentifier)
    {
        $number = (int) substr($currentIdentifier, 3);
        $nextNumber = $number + 1;
        return 'RG-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
