<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetUniqueIdentifiersRobust extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customers:reset-unique-identifiers-robust';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset all unique identifiers with robust handling for large datasets';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🔄 Starting robust unique identifier reset...');
        
        try {
            // Step 1: Check current state
            $totalCustomers = DB::table('customers')->count();
            $this->info("📊 Total customers: {$totalCustomers}");
            
            // Step 2: Set all unique_identifier to NULL
            $this->info('🗑️  Setting all unique_identifier to NULL...');
            DB::statement('UPDATE customers SET unique_identifier = NULL');
            $this->info('✅ All unique_identifier fields are now NULL');
            
            // Step 3: Use a more robust approach with window functions
            $this->info('🔢 Assigning sequential unique identifiers...');
            
            // First, let's try a different approach using a subquery
            DB::statement("
                UPDATE customers c1
                SET unique_identifier = CONCAT('RG-', LPAD(
                    (SELECT COUNT(*) + 1 
                     FROM customers c2 
                     WHERE c2.created_at < c1.created_at 
                     OR (c2.created_at = c1.created_at AND c2.id < c1.id)
                    ), 3, '0'))
            ");
            
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
                $this->info('📋 Examples of updated records:');
                $examples = DB::select('SELECT id, mteja, mpokeaji, unique_identifier, created_at FROM customers WHERE unique_identifier IS NOT NULL ORDER BY created_at ASC LIMIT 5');
                
                foreach ($examples as $customer) {
                    $this->line("ID: {$customer->id} | Unique ID: {$customer->unique_identifier} | Mteja: {$customer->mteja} | Created: {$customer->created_at}");
                }
                
                $this->info('🎉 Reset process completed successfully!');
                return 0;
            } else {
                $this->error("❌ ERROR: Found {$result->duplicates} duplicate identifiers!");
                
                // Try alternative approach
                $this->info('🔄 Trying alternative approach...');
                return $this->alternativeApproach();
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }
    }
    
    private function alternativeApproach()
    {
        try {
            $this->info('🔄 Using alternative batch approach...');
            
            // Clear all identifiers again
            DB::statement('UPDATE customers SET unique_identifier = NULL');
            
            // Process in smaller batches
            $batchSize = 10000;
            $offset = 0;
            $counter = 1;
            
            do {
                $customers = DB::table('customers')
                    ->orderBy('created_at', 'asc')
                    ->orderBy('id', 'asc')
                    ->offset($offset)
                    ->limit($batchSize)
                    ->get(['id']);
                
                if ($customers->isEmpty()) {
                    break;
                }
                
                foreach ($customers as $customer) {
                    $uniqueIdentifier = 'RG-' . str_pad($counter, 3, '0', STR_PAD_LEFT);
                    
                    DB::table('customers')
                        ->where('id', $customer->id)
                        ->update(['unique_identifier' => $uniqueIdentifier]);
                    
                    $counter++;
                }
                
                $offset += $batchSize;
                $this->info("Processed batch: {$offset}");
                
            } while ($customers->count() === $batchSize);
            
            // Final verification
            $result = DB::select('SELECT COUNT(*) as total_customers, COUNT(DISTINCT unique_identifier) as unique_identifiers, COUNT(*) - COUNT(DISTINCT unique_identifier) as duplicates FROM customers WHERE unique_identifier IS NOT NULL')[0];
            
            if ($result->duplicates == 0) {
                $this->info('✅ SUCCESS: All identifiers are unique!');
                return 0;
            } else {
                $this->error("❌ Still have duplicates: {$result->duplicates}");
                return 1;
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Alternative approach failed: ' . $e->getMessage());
            return 1;
        }
    }
}
