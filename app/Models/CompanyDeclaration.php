<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyDeclaration extends Model
{
    use HasFactory;

    protected $table = 'company_declarations';
    protected $fillable = [
        'companyName2',
        'application',
        'applicationDate',
        'authorizedName',
        'agree'
    ];
}
