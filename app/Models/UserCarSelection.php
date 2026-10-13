<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserCarSelection extends Model
{

    

    protected $table = 'users_cars_selection';

    protected $fillable = [
         'user_id','car_id','company_id',
    ];
   
    

 
}
