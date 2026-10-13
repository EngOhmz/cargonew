<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetUniqueIdentifiersDirect extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customers:reset-unique-identifiers-direct';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Direct reset of unique identifiers based on customer ID';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🔄 Starting direct reset of unique identifiers...');
        
        try {
            // Step 1: Set all unique_identifier to NULL
            $this->info('🗑️  Setting all unique_identifier to NULL...');
            DB::statement('UPDATE customers SET unique_identifier = NULL');
            $this->info('✅ All unique_identifier fields are now NULL');
            
            // Step 2: Assign unique identifiers based on ID
            $this->info('🔢 Assigning unique identifiers based on customer ID...');
            DB::statement('UPDATE customers SET unique_identifier = CONCAT(\'RG-\', LPAD(id, 3, \'0\')) WHERE id IS NOT NULL');
            $this->info('✅ Unique identifiers assigned based on ID');
            
            // Step 3: Quick verification
            $this->info('🔍 Verifying results...');
            $result = DB::select('SELECT COUNT(*) as total_customers, COUNT(DISTINCT unique_identifier) as unique_identifiers, COUNT(*) - COUNT(DISTINCT unique_identifier) as duplicates FROM customers WHERE unique_identifier IS NOT NULL')[0];
            
            $this->info("📊 Results:");
            $this->info("   Total customers: {$result->total_customers}");
            $this->info("   Unique identifiers: {$result->unique_identifiers}");
            $this->info("   Duplicates: {$result->duplicates}");
            
            if ($result->duplicates == 0) {
                $this->info('✅ SUCCESS: All identifiers are unique!');
                
                // Show first few examples
                $this->info('📋 First 5 records:');
                $examples = DB::select('SELECT id, unique_identifier, mteja FROM customers WHERE unique_identifier IS NOT NULL ORDER BY id ASC LIMIT 5');
                foreach ($examples as $customer) {
                    $this->line("ID: {$customer->id} → {$customer->unique_identifier} | Mteja: {$customer->mteja}");
                }
                
                // Show highest ID
                $highestId = DB::table('customers')->max('id');
                $highestIdentifier = 'RG-' . str_pad($highestId, 3, '0', STR_PAD_LEFT);
                $nextIdentifier = 'RG-' . str_pad($highestId + 1, 3, '0', STR_PAD_LEFT);
                
                $this->info("🎯 Highest ID: {$highestId} → {$highestIdentifier}");
                $this->info("   Next new entry will get: {$nextIdentifier}");
                
                $this->info('🎉 Reset completed successfully!');
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
