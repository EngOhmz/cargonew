<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetUniqueIdentifiersById extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customers:reset-unique-identifiers-by-id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset unique identifiers based on customer ID (RG-001, RG-002, etc.)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🔄 Starting unique identifier reset based on customer ID...');
        
        try {
            // Step 1: Check current state
            $totalCustomers = DB::table('customers')->count();
            $this->info("📊 Total customers: {$totalCustomers}");
            
            // Step 2: Set all unique_identifier to NULL
            $this->info('🗑️  Setting all unique_identifier to NULL...');
            DB::statement('UPDATE customers SET unique_identifier = NULL');
            $this->info('✅ All unique_identifier fields are now NULL');
            
            // Step 3: Assign unique identifiers based on ID
            $this->info('🔢 Assigning unique identifiers based on customer ID...');
            DB::statement('UPDATE customers SET unique_identifier = CONCAT(\'RG-\', LPAD(id, 3, \'0\')) WHERE id IS NOT NULL');
            
            // Step 4: Verify results
            $this->info('🔍 Verifying results...');
            $result = DB::select('SELECT COUNT(*) as total_customers, COUNT(DISTINCT unique_identifier) as unique_identifiers, COUNT(*) - COUNT(DISTINCT unique_identifier) as duplicates FROM customers WHERE unique_identifier IS NOT NULL')[0];
            
            $this->info("📊 Final state:");
            $this->info("   Total customers: {$result->total_customers}");
            $this->info("   Unique identifiers: {$result->unique_identifiers}");
            $this->info("   Duplicates: {$result->duplicates}");
            
            if ($result->duplicates == 0) {
                $this->info('✅ SUCCESS: All identifiers are unique!');
                
                // Show examples
                $this->info('📋 Examples of updated records (first 10):');
                $examples = DB::select('SELECT id, mteja, mpokeaji, unique_identifier, created_at FROM customers WHERE unique_identifier IS NOT NULL ORDER BY id ASC LIMIT 10');
                
                foreach ($examples as $customer) {
                    $this->line("ID: {$customer->id} | Unique ID: {$customer->unique_identifier} | Mteja: {$customer->mteja}");
                }
                
                // Show highest ID
                $highest = DB::select('SELECT MAX(unique_identifier) as highest_identifier FROM customers WHERE unique_identifier IS NOT NULL')[0];
                $this->info("🎯 Highest unique identifier: {$highest->highest_identifier}");
                
                // Calculate next identifier
                $highestId = DB::table('customers')->max('id');
                $nextIdentifier = 'RG-' . str_pad($highestId + 1, 3, '0', STR_PAD_LEFT);
                $this->info("   Next new entry will get: {$nextIdentifier}");
                
                $this->info('🎉 Reset process completed successfully!');
                return 0;
            } else {
                $this->error("❌ ERROR: Found {$result->duplicates} duplicate identifiers!");
                return 1;
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }
    }
}
