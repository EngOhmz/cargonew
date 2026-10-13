<?php

namespace App\Models\Customer;

use App\Traits\HasTenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Nicolaslopezj\Searchable\SearchableTrait;

class Customer extends Model
{
    use HasFactory, HasTenantScope;

    protected $guarded = ['id'];
    protected $fillable = ['mteja', 'mpokeaji', 'customer_phone', 'customer_location', 'receiver_phone', 'unique_identifier', 'added_by', 'company_id', 'status'];

    use Notifiable;
    
    // use SearchableTrait;

    protected $searchable = [
    'columns' => [
    'customers.mteja'  => 10,
    'customers.mpokeaji'   => 10,
    'customers.customer_phone'   => 10,
    'customers.receiver_phone'   => 10,
    'customers.created_at'   => 10,
    'customers.id'    => 10,
    ]
    ];


    public function customerPacel(){

        return $this->hasMany('App\Models\Customer\CustomerPacel','pacel_id');
    }

    public function registeredBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'added_by');
    }
}
