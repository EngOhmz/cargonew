<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Customer\Customer;

class UpdateExistingCustomersWithUniqueIdentifiers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Get all existing customers ordered by creation date (oldest first)
        $customers = Customer::whereNull('unique_identifier')
            ->orderBy('created_at', 'asc')
            ->get();

        $counter = 1;

        foreach ($customers as $customer) {
            // Generate unique identifier starting from RG-001
            $uniqueIdentifier = 'RG-' . str_pad($counter, 3, '0', STR_PAD_LEFT);
            
            // Update the customer record
            $customer->update([
                'unique_identifier' => $uniqueIdentifier
            ]);
            
            $counter++;
        }

        // Log the results
        echo "Updated " . ($counter - 1) . " customers with unique identifiers\n";
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove unique identifiers from all customers
        Customer::whereNotNull('unique_identifier')->update([
            'unique_identifier' => null
        ]);
        
        echo "Removed unique identifiers from all customers\n";
    }
}