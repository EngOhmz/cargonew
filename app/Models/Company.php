<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $table = 'companies';
    protected $fillable = [
        'cname',
        'mandatory_id',
        'companyDeclarartion_id',
        'organization',
        'regDate',
        'regNo',
        'employeeNo',

        'regHead',
        'employeeBox',
        'tinNo',
        'phone',


        'contactName',
        'email',
        'personalPhone',
        'app_name',
        'app_logo',
        'logo',
        'trial_start_at',
        'trial_end_at',
        'subscription_start_at',
        'subscription_end_at',
        'is_disabled',
        'is_platform',
        'sms_sender_id',
    ];

    protected $casts = [
        'trial_start_at' => 'datetime',
        'trial_end_at' => 'datetime',
        'subscription_start_at' => 'datetime',
        'subscription_end_at' => 'datetime',
        'is_disabled' => 'boolean',
        'is_platform' => 'boolean',
    ];

    public function membership_fees(){
        return $this->hasMany('App\Models\Member\MembershipFee','company_id');
    }
    public function membership_payments(){
        return $this->hasMany('App\Models\Member\MembershipPayment','company_id');
    }

    public function users(){
        return $this->hasMany('App\Models\User','company_id');
    }

    public function smsMessages()
    {
        return $this->hasMany(Sms::class, 'company_id');
    }

}
