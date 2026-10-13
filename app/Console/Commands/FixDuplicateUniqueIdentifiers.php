<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer\Customer;
use Illuminate\Support\Facades\DB;

class FixDuplicateUniqueIdentifiers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customers:fix-duplicate-identifiers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix duplicate unique identifiers by reassigning them properly';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting to fix duplicate unique identifiers...');
        
        // First, let's check the current state
        $totalCustomers = Customer::count();
        $customersWithIdentifiers = Customer::whereNotNull('unique_identifier')->count();
        $uniqueIdentifiers = Customer::whereNotNull('unique_identifier')
            ->distinct('unique_identifier')
            ->count('unique_identifier');
        
        $this->info("Total customers: {$totalCustomers}");
        $this->info("Customers with identifiers: {$customersWithIdentifiers}");
        $this->info("Unique identifiers: {$uniqueIdentifiers}");
        
        if ($customersWithIdentifiers > $uniqueIdentifiers) {
            $duplicates = $customersWithIdentifiers - $uniqueIdentifiers;
            $this->warn("Found {$duplicates} duplicate identifiers!");
        } else {
            $this->info("No duplicates found.");
            return 0;
        }
        
        // Clear all existing unique identifiers
        $this->info('Clearing all existing unique identifiers...');
        Customer::whereNotNull('unique_identifier')->update(['unique_identifier' => null]);
        
        // Get all customers ordered by creation date
        $customers = Customer::orderBy('created_at', 'asc')->get();
        
        $this->info("Reassigning unique identifiers to {$customers->count()} customers...");
        
        $counter = 1;
        $progressBar = $this->output->createProgressBar($customers->count());
        
        foreach ($customers as $customer) {
            // Generate unique identifier starting from RG-001
            $uniqueIdentifier = 'RG-' . str_pad($counter, 3, '0', STR_PAD_LEFT);
            
            // Update the customer record
            $customer->update([
                'unique_identifier' => $uniqueIdentifier
            ]);
            
            $counter++;
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine();
        
        // Verify the fix
        $finalUniqueIdentifiers = Customer::whereNotNull('unique_identifier')
            ->distinct('unique_identifier')
            ->count('unique_identifier');
        $finalCustomersWithIdentifiers = Customer::whereNotNull('unique_identifier')->count();
        
        $this->info("Fix completed!");
        $this->info("Final customers with identifiers: {$finalCustomersWithIdentifiers}");
        $this->info("Final unique identifiers: {$finalUniqueIdentifiers}");
        
        if ($finalCustomersWithIdentifiers === $finalUniqueIdentifiers) {
            $this->info("✅ SUCCESS: All identifiers are now unique!");
        } else {
            $this->error("❌ ERROR: Still have duplicates!");
        }
        
        // Show some examples
        $this->info('Examples of fixed records:');
        Customer::whereNotNull('unique_identifier')
            ->orderBy('created_at', 'asc')
            ->take(5)
            ->get(['id', 'mteja', 'mpokeaji', 'unique_identifier', 'created_at'])
            ->each(function($customer) {
                $this->line("ID: {$customer->id} | Unique ID: {$customer->unique_identifier} | Mteja: {$customer->mteja} | Created: {$customer->created_at}");
            });
        
        return 0;
    }
}
