<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetUniqueIdentifiersSQL extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customers:reset-unique-identifiers-sql';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset all unique identifiers using efficient SQL operations';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🔄 Starting unique identifier reset using SQL...');
        
        try {
            // Step 1: Set all unique_identifier to NULL
            $this->info('🗑️  Setting all unique_identifier to NULL...');
            DB::statement('UPDATE customers SET unique_identifier = NULL');
            $this->info('✅ All unique_identifier fields are now NULL');
            
            // Step 2: Add temporary column
            $this->info('📝 Adding temporary row number column...');
            DB::statement('ALTER TABLE customers ADD COLUMN temp_row_number INT');
            
            // Step 3: Set row numbers
            $this->info('🔢 Setting row numbers based on creation date...');
            DB::statement('SET @row_number = 0');
            DB::statement('UPDATE customers SET temp_row_number = (@row_number := @row_number + 1) ORDER BY created_at ASC');
            
            // Step 4: Update unique_identifier
            $this->info('🔄 Updating unique_identifier with sequential values...');
            DB::statement('UPDATE customers SET unique_identifier = CONCAT(\'RG-\', LPAD(temp_row_number, 3, \'0\')) WHERE temp_row_number IS NOT NULL');
            
            // Step 5: Remove temporary column
            $this->info('🧹 Removing temporary column...');
            DB::statement('ALTER TABLE customers DROP COLUMN temp_row_number');
            
            // Step 6: Verify results
            $this->info('🔍 Verifying results...');
            $result = DB::select('SELECT COUNT(*) as total_customers, COUNT(DISTINCT unique_identifier) as unique_identifiers, COUNT(*) - COUNT(DISTINCT unique_identifier) as duplicates FROM customers WHERE unique_identifier IS NOT NULL')[0];
            
            $this->info("📊 Final state:");
            $this->info("   Total customers: {$result->total_customers}");
            $this->info("   Unique identifiers: {$result->unique_identifiers}");
            $this->info("   Duplicates: {$result->duplicates}");
            
            if ($result->duplicates == 0) {
                $this->info('✅ SUCCESS: All identifiers are unique!');
            } else {
                $this->error("❌ ERROR: Found {$result->duplicates} duplicate identifiers!");
                return 1;
            }
            
            // Show examples
            $this->info('📋 Examples of updated records:');
            $examples = DB::select('SELECT id, mteja, mpokeaji, unique_identifier, created_at FROM customers WHERE unique_identifier IS NOT NULL ORDER BY created_at ASC LIMIT 5');
            
            foreach ($examples as $customer) {
                $this->line("ID: {$customer->id} | Unique ID: {$customer->unique_identifier} | Mteja: {$customer->mteja} | Created: {$customer->created_at}");
            }
            
            $this->info('🎉 Reset process completed successfully!');
            return 0;
            
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }
    }
}
