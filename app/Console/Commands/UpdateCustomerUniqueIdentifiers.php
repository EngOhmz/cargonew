<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer\Customer;

class UpdateCustomerUniqueIdentifiers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customers:update-unique-identifiers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update existing customers with unique identifiers in RG-001, RG-002 format';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting to update customers with unique identifiers...');
        
        // Get all existing customers ordered by creation date (oldest first)
        $customers = Customer::whereNull('unique_identifier')
            ->orderBy('created_at', 'asc')
            ->get();

        if ($customers->count() === 0) {
            $this->info('No customers found without unique identifiers.');
            return 0;
        }

        $this->info("Found {$customers->count()} customers to update.");

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
        
        $this->info("Successfully updated " . ($counter - 1) . " customers with unique identifiers.");
        
        // Show some examples
        $this->info('Examples of updated records:');
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